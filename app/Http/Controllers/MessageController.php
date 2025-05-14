<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Events\MessageSent;

class MessageController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::guard('admin')->check() ? Auth::guard('admin')->user() : Auth::user();
            $userType = Auth::guard('admin')->check() ? 'admin' : 'intern';

            if ($userType === 'admin') {
                $interns = User::whereHas('role', function($query) {
                    $query->where('name', 'intern');
                })->get();
                
                return view('chat.index', [
                    'interns' => $interns,
                    'userType' => $userType,
                    'currentUser' => $user
                ]);
            } else {
                $admins = Admin::all();
                return view('chat.index', [
                    'admins' => $admins,
                    'userType' => $userType,
                    'currentUser' => $user
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to load chat index', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to load chat. Please try again.');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'content' => 'required|string',
                'receiver_type' => 'required|string',
                'receiver_id' => 'required|integer'
            ]);

            $sender = Auth::guard('admin')->check() ? Auth::guard('admin')->user() : Auth::user();
            
            $message = Message::create([
                'content' => $request->content,
                'sender_type' => get_class($sender),
                'sender_id' => $sender->id,
                'receiver_type' => $request->receiver_type,
                'receiver_id' => $request->receiver_id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => $message
            ]);

        } catch (\Exception $e) {
            Log::error('Message creation failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send message'
            ], 500);
        }
    }

    public function getMessages(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|integer',
                'user_type' => 'required|string|in:intern,admin'
            ]);

            $currentUser = Auth::guard('admin')->check() ? Auth::guard('admin')->user() : Auth::user();
            $currentUserType = get_class($currentUser);
            
            $otherUserType = $request->user_type === 'intern' ? User::class : Admin::class;

            $messages = Message::where(function($query) use ($currentUser, $currentUserType, $request, $otherUserType) {
                // Messages sent by current user to the other user
                $query->where([
                    'sender_type' => $currentUserType,
                    'sender_id' => $currentUser->id,
                    'receiver_type' => $otherUserType,
                    'receiver_id' => $request->user_id
                ])->orWhere(function($q) use ($currentUser, $currentUserType, $request, $otherUserType) {
                    // Messages received by current user from the other user
                    $q->where([
                        'sender_type' => $otherUserType,
                        'sender_id' => $request->user_id,
                        'receiver_type' => $currentUserType,
                        'receiver_id' => $currentUser->id
                    ]);
                });
            })
            ->orderBy('created_at', 'asc')
            ->get();

            return response()->json(['messages' => $messages]);
        } catch (\Exception $e) {
            Log::error('Failed to get messages', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to load messages'], 500);
        }
    }

    public function markAsRead(Message $message)
    {
        try {
            $user = Auth::guard('admin')->check() ? Auth::guard('admin')->user() : Auth::user();
            
            if ($message->receiver_id === $user->id) {
                $message->update(['read_at' => now()]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Failed to mark message as read', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to mark message as read'], 500);
        }
    }
}
