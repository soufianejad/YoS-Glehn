<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;

class AdminMessagingController extends Controller
{
    /**
     * Display a listing of the conversations.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $conversations = Conversation::with('participants', 'latestMessage')->latest('updated_at')->get();

        return view('admin.messaging.index', compact('conversations'));
    }

    /**
     * Display the specified conversation.
     *
     * @return \Illuminate\View\View
     */
    public function show(Conversation $conversation)
    {
        $conversation->load('messages.sender', 'participants');

        return view('admin.messaging.show', compact('conversation'));
    }

    /**
     * Remove the specified conversation from storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Conversation $conversation)
    {
        $conversation->delete();

        return redirect()->route('admin.messaging.index')->with('success', 'Conversation deleted successfully.');
    }

    /**
     * Toggle a user's ability to send and receive messages.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleUserMessageReception(Request $request, User $user)
    {
        $request->validate([
            'can_receive_messages' => 'required|boolean',
        ]);

        $user->can_receive_messages = $request->can_receive_messages;
        $user->save();

        return response()->json([
            'message' => 'User messaging reception status updated successfully.',
            'can_receive_messages' => $user->can_receive_messages,
        ]);
    }
}
