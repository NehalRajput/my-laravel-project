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
            
            if (!$sender) {
                throw new \Exception('Unauthorized: No authenticated user found');
            }

            // Validate receiver exists
            $receiverClass = $request->receiver_type;
            $receiver = $receiverClass::find($request->receiver_id);
            
            if (!$receiver) {
                throw new \Exception('Invalid receiver: User not found');
            }
            
            $message = Message::create([
                'content' => $request->content,
                'sender_type' => get_class($sender),
                'sender_id' => $sender->id,
                'receiver_type' => $request->receiver_type,
                'receiver_id' => $request->receiver_id,
            ]);

            // Add sender name to the message
            $message->sender_name = $sender->name;

            try {
                // Broadcast the message
                broadcast(new MessageSent($message))->toOthers();
            } catch (\Exception $e) {
                Log::error('Broadcasting failed: ' . $e->getMessage(), [
                    'message_id' => $message->id,
                    'sender_id' => $sender->id,
                    'receiver_id' => $request->receiver_id
                ]);
                // Don't throw here - message is saved even if broadcast fails
            }

            return response()->json([
                'status' => 'success',
                'message' => $message,
                'current_user' => [
                    'id' => $sender->id,
                    'type' => get_class($sender),
                    'name' => $sender->name
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Message creation failed: ' . $e->getMessage(), [
                'sender_id' => $sender->id ?? null,
                'receiver_id' => $request->receiver_id ?? null,
                'content' => $request->content ?? null
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send message: ' . $e->getMessage()
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
            $otherUser = $otherUserType::find($request->user_id);

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
            ->get()
            ->map(function($message) use ($currentUser, $otherUser) {
                $message->sender_name = $message->sender_id === $currentUser->id ? $currentUser->name : $otherUser->name;
                return $message;
            });

            return response()->json([
                'messages' => $messages,
                'current_user' => [
                    'id' => $currentUser->id,
                    'type' => get_class($currentUser),
                    'name' => $currentUser->name
                ]
            ]);
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
