<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessagingController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display the list of conversations for the current user.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $recipient = null;
        $activeConversation = null;

        // Handle pre-selected recipient from URL to start a new conversation
        if ($request->has('recipient_id')) {
            $recipientId = $request->input('recipient_id');
            if ($recipientId != $user->id) {
                $recipient = User::find($recipientId);

                // If a recipient is found, check if a private conversation with this user already exists.
                if ($recipient) {
                    $activeConversation = $user->conversations()
                        ->where('type', 'private')
                        ->whereHas('participants', function ($query) use ($recipientId) {
                            $query->where('user_id', $recipientId);
                        })
                        ->whereHas('participants', null, '=', 2) // Ensures it's a 2-person conversation
                        ->first();
                }
            }
        }

        // Handle selecting an existing conversation from the list
        if ($request->has('conversation_id')) {
            $activeConversation = $user->conversations()->find($request->input('conversation_id'));
        }

        $conversations = $user->conversations()
            ->wherePivotNull('archived_at') // Exclude archived conversations
            ->wherePivotNull('deleted_at')   // Exclude soft-deleted conversations
            ->with(['participants' => function ($query) use ($user) {
                // Eager load participants, but exclude the current user from the collection
                $query->where('user_id', '!=', $user->id);
            }, 'latestMessage'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('messaging.index', compact('conversations', 'recipient', 'activeConversation'));
    }

    /**
     * Display the messages for a specific conversation.
     *
     * @param  int  $conversationId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);
        $user = Auth::user();

        // Authorize that the user is part of this conversation
        if (! $user->conversations->contains($conversation)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $messages = $conversation->messages()->with('sender')->get();

        // Mark messages as read for the current user in this conversation
        $user->conversations()->updateExistingPivot($conversation->id, ['last_read_at' => now()]);

        return response()->json($messages);
    }

    /**
     * Send a new message.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'content' => 'required|string',
        ]);

        $conversation = Conversation::findOrFail($request->conversation_id);
        $sender = Auth::user();

        // Authorize that the user is part of this conversation
        if (! $sender->conversations->contains($conversation)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'content' => $request->content,
        ]);

        // Update conversation's updated_at timestamp
        $conversation->touch();

        // Mark the sender's participation as read
        $sender->conversations()->updateExistingPivot($conversation->id, ['last_read_at' => now()]);

        // Notify all other participants
        foreach ($conversation->participants as $participant) {
            if ($participant->id !== $sender->id) {
                $this->notificationService->sendNotification(
                    $participant,
                    'Nouveau message reçu',
                    "Vous avez reçu un nouveau message de {$sender->name}.",
                    route('messaging.index', ['conversation' => $conversation->id]),
                    'info'
                );
            }
        }

        return response()->json($message->load('sender'));
    }

    public function storeConversationAndFirstMessage(Request $request)
    {
        $request->validate([
            'recipient_ids' => 'required|array|min:1',
            'recipient_ids.*' => 'exists:users,id',
            'content' => 'required|string',
            'name' => 'nullable|string|max:255',
        ]);

        $sender = Auth::user();
        $recipients = User::whereIn('id', $request->recipient_ids)->get();

        // Ensure all recipients can receive messages
        foreach ($recipients as $recipient) {
            if (! $recipient->can_receive_messages) {
                return redirect()->route('messaging.index')->with('error', "User {$recipient->name} cannot receive messages.");
            }
        }

        $conversation = null;
        
        DB::transaction(function () use ($request, $sender, $recipients, &$conversation) {
            $participantIds = array_merge($request->recipient_ids, [$sender->id]);
            
            if (count($participantIds) == 2) { // Private conversation
                $conversation = $sender->conversations()
                    ->where('type', 'private')
                    ->whereHas('participants', function ($query) use ($recipients) {
                        $query->where('user_id', $recipients->first()->id);
                    }, '=', 1)
                    ->whereHas('participants', function ($query) use ($sender) {
                        $query->where('user_id', $sender->id);
                    }, '=', 1)
                    ->first();
            }

            if (!$conversation) {
                $conversation = Conversation::create([
                    'name' => count($participantIds) > 2 ? $request->name : null,
                    'type' => count($participantIds) > 2 ? 'group' : 'private',
                ]);
                $conversation->participants()->attach($participantIds);
            }

            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $sender->id,
                'content' => $request->content,
            ]);
            
            $conversation->touch();

            // Add notification logic for the first message
            foreach ($recipients as $recipient) {
                $this->notificationService->sendNotification(
                    $recipient,
                    'Nouveau message reçu',
                    "Vous avez une nouvelle conversation avec {$sender->name}.",
                    route('messaging.index', ['conversation' => $conversation->id]),
                    'info'
                );
            }
        });

        return redirect()->route('messaging.index', ['conversation' => $conversation->id]);
    }

    public function getNewMessages($conversationId, Request $request)
    {
        $conversation = Conversation::findOrFail($conversationId);
        $user = Auth::user();

        // Authorize that the user is part of this conversation
        if (! $user->conversations->contains($conversation)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $afterId = $request->query('after_id', 0);

        $messages = $conversation->messages()
            ->with('sender')
            ->where('id', '>', $afterId)
            ->orderBy('created_at', 'asc')
            ->get();

        // Update last_read_at for the current user if new messages are fetched
        if ($messages->count() > 0) {
            $user->conversations()->updateExistingPivot($conversation->id, ['last_read_at' => now()]);
        }

        return response()->json($messages);
    }

    public function getMessageableUsers()
    {
        $users = User::where('id', '!=', Auth::id())
            ->where('can_receive_messages', 1)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name']);

        // Combine first and last name for a full name attribute
        $users->each(function ($user) {
            $user->full_name = $user->first_name.' '.$user->last_name;
        });

        return response()->json($users);
    }

    /**
     * Display the list of archived conversations for the current user.
     *
     * @return \Illuminate\View\View
     */
    public function archivedConversations()
    {
        $user = Auth::user();
        $conversations = $user->conversations()
            ->wherePivotNotNull('archived_at') // Only archived conversations
            ->wherePivotNull('deleted_at')     // Exclude soft-deleted conversations
            ->with('participants')
            ->latest('updated_at')
            ->get();

        return view('messaging.archived', compact('conversations'));
    }
}
