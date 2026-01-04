<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\Request;

class MessagingController extends Controller
{
    public function index(Request $request)
    {
        $query = Conversation::with('participants', 'latestMessage')->latest();

        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->whereHas('participants', function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%'.$searchTerm.'%');
            });
        }

        $conversations = $query->paginate(15);

        return view('admin.messaging.index', [
            'conversations' => $conversations,
            'search' => $request->input('search', ''),
        ]);
    }

    public function show(Conversation $conversation)
    {
        $messages = $conversation->messages()->with('sender')->orderBy('created_at', 'asc')->get();

        return response()->json($messages);
    }

    public function getNewMessages(Request $request, Conversation $conversation)
    {
        $afterId = $request->query('after_id', 0);

        $messages = $conversation->messages()
            ->with('sender')
            ->where('id', '>', $afterId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }
}
