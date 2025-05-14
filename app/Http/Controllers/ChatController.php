<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use App\Http\Requests\ChatRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Exception;

class ChatController extends Controller
{
    public function index()
    {
        return view('chat.index');
    }

    public function sendMessage(ChatRequest $request)
    {
        try {
            $sender = Auth::user();
            
            // Handle file upload if present
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('chat_attachments', 'public');
            }

            $message = Message::create([
                'sender_id' => $sender->id,
                'recipient_id' => $request->recipient_id,
                'message' => $request->message,
                'attachment' => $attachmentPath,
                'message_type' => $request->message_type
            ]);

            Log::info('Message sent successfully', [
                'message_id' => $message->id,
                'sender_id' => $sender->id,
                'recipient_id' => $request->recipient_id
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Message sent successfully'
            ]);

        } catch (Exception $e) {
            Log::error('Failed to send message', [
                'error' => $e->getMessage(),
                'sender_id' => Auth::id(),
                'recipient_id' => $request->recipient_id ?? null
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send message. Please try again.'
            ], 500);
        }
    }

    public function getMessages(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id'
            ]);

            $currentUser = Auth::user();
            $otherUser = $request->user_id;

            $messages = Message::where(function($query) use ($currentUser, $otherUser) {
                $query->where('sender_id', $currentUser->id)
                    ->where('recipient_id', $otherUser);
            })->orWhere(function($query) use ($currentUser, $otherUser) {
                $query->where('sender_id', $otherUser)
                    ->where('recipient_id', $currentUser->id);
            })
            ->with(['sender:id,name', 'recipient:id,name'])
            ->orderBy('created_at', 'asc')
            ->get();

            return response()->json([
                'status' => 'success',
                'messages' => $messages
            ]);

        } catch (Exception $e) {
            Log::error('Failed to fetch messages', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'other_user_id' => $request->user_id ?? null
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load messages. Please try again.'
            ], 500);
        }
    }
} 