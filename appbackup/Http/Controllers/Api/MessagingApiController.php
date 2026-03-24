<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessagingApiController extends Controller
{
    public function show($conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);
        $user = Auth::user();

        if (! $user->conversations->contains($conversation)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $messages = $conversation->messages()->with('sender')->get();

        $user->conversations()->updateExistingPivot($conversation->id, ['last_read_at' => now()]);

        return response()->json($messages);
    }

    public function getNewMessages(Request $request, Conversation $conversation)
    {
        $user = Auth::user();

        if (! $user->conversations->contains($conversation)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $afterId = $request->query('after_id', 0);

        $messages = $conversation->messages()
            ->with('sender')
            ->where('id', '>', $afterId)
            ->orderBy('created_at', 'asc')
            ->get();

        if ($messages->count() > 0) {
            $user->conversations()->updateExistingPivot($conversation->id, ['last_read_at' => now()]);
        }

        return response()->json($messages);
    }
}
