<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Models\Admin;
use App\Events\NewMessage;
use App\Http\Requests\MessageRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            $userType = get_class($user);
            
            $conversations = Message::where(function($query) use ($user, $userType) {
                    $query->where('sender_type', $userType)
                        ->where('sender_id', $user->id);
                })
                ->orWhere(function($query) use ($user, $userType) {
                    $query->where('receiver_type', $userType)
                        ->where('receiver_id', $user->id);
                })
                ->with(['sender', 'receiver'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy(function($message) use ($user) {
                    return $message->sender_id == $user->id 
                        ? $message->receiver_id . '-' . $message->receiver_type
                        : $message->sender_id . '-' . $message->sender_type;
                });

            $users = User::where('id', '!=', Auth::id())->get();
            $admins = Admin::all();

            Log::info('Messages index loaded successfully', [
                'user_id' => Auth::id(),
                'conversation_count' => $conversations->count()
            ]);

            return view('messages.index', compact('conversations', 'users', 'admins'));
        } catch (\Exception $e) {
            Log::error('Failed to load messages index', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Failed to load messages. Please try again.');
        }
    }

    public function show($receiverType, $receiverId)
    {
        try {
            $user = Auth::user();
            $userType = get_class($user);
            
            $receiver = $receiverType === 'App\\Models\\Admin' 
                ? Admin::findOrFail($receiverId)
                : User::findOrFail($receiverId);

            $messages = Message::where(function($query) use ($user, $userType, $receiver, $receiverType) {
                    $query->where('sender_type', $userType)
                        ->where('sender_id', $user->id)
                        ->where('receiver_type', $receiverType)
                        ->where('receiver_id', $receiver->id);
                })
                ->orWhere(function($query) use ($user, $userType, $receiver, $receiverType) {
                    $query->where('sender_type', $receiverType)
                        ->where('sender_id', $receiver->id)
                        ->where('receiver_type', $userType)
                        ->where('receiver_id', $user->id);
                })
                ->with(['sender', 'receiver'])
                ->orderBy('created_at', 'asc')
                ->get();

            DB::transaction(function() use ($user, $userType, $receiverType, $receiver) {
                Message::where('receiver_type', $userType)
                    ->where('receiver_id', $user->id)
                    ->where('sender_type', $receiverType)
                    ->where('sender_id', $receiver->id)
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);
            });

            Log::info('Conversation loaded successfully', [
                'user_id' => Auth::id(),
                'receiver_id' => $receiverId,
                'message_count' => $messages->count()
            ]);

            return view('messages.show', compact('messages', 'receiver'));
        } catch (\Exception $e) {
            Log::error('Failed to load conversation', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'receiver_id' => $receiverId
            ]);
            return redirect()->route('messages.index')->with('error', 'Failed to load conversation. Please try again.');
        }
    }

    public function store(MessageRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $userType = get_class($user);

            $message = Message::create([
                'content' => $request->content,
                'sender_type' => $userType,
                'sender_id' => $user->id,
                'receiver_type' => $request->receiver_type,
                'receiver_id' => $request->receiver_id
            ]);

            $message->load(['sender', 'receiver']);
            broadcast(new NewMessage($message))->toOthers();

            DB::commit();

            Log::info('Message sent successfully', [
                'message_id' => $message->id,
                'sender_id' => $user->id,
                'receiver_id' => $request->receiver_id
            ]);

            return response()->json($message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to send message', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'receiver_id' => $request->receiver_id
            ]);
            return response()->json(['error' => 'Failed to send message. Please try again.'], 500);
        }
    }

    public function getMessages(MessageRequest $request)
    {
        try {
            $messages = Message::where(function($query) use ($request) {
                $query->where([
                    'sender_id' => Auth::id(),
                    'receiver_id' => $request->user_id,
                ])->orWhere([
                    'sender_id' => $request->user_id,
                    'receiver_id' => Auth::id(),
                ]);
            })
            ->orderBy('created_at', 'asc')
            ->get();

            Log::info('Messages fetched successfully', [
                'user_id' => Auth::id(),
                'other_user_id' => $request->user_id,
                'message_count' => $messages->count()
            ]);

            return response()->json(['messages' => $messages]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch messages', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'other_user_id' => $request->user_id
            ]);
            return response()->json(['error' => 'Failed to fetch messages. Please try again.'], 500);
        }
    }

    public function markAsRead(Message $message)
    {
        try {
            if ($message->receiver_id === Auth::id()) {
                DB::transaction(function() use ($message) {
                    $message->update(['read_at' => now()]);
                });

                Log::info('Message marked as read', [
                    'message_id' => $message->id,
                    'user_id' => Auth::id()
                ]);
            }
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Failed to mark message as read', [
                'error' => $e->getMessage(),
                'message_id' => $message->id,
                'user_id' => Auth::id()
            ]);
            return response()->json(['error' => 'Failed to mark message as read.'], 500);
        }
    }

    public function markAllAsRead($userId)
    {
        try {
            DB::transaction(function() use ($userId) {
                Message::where([
                    'sender_id' => $userId,
                    'receiver_id' => Auth::id(),
                    'read_at' => null,
                ])->update(['read_at' => now()]);
            });

            Log::info('All messages marked as read', [
                'user_id' => Auth::id(),
                'sender_id' => $userId
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Failed to mark all messages as read', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'sender_id' => $userId
            ]);
            return response()->json(['error' => 'Failed to mark messages as read.'], 500);
        }
    }

    public function getUsers()
    {
        try {
            $users = $this->getAvailableUsers();

            Log::info('Available users fetched successfully', [
                'user_id' => Auth::id(),
                'user_count' => $users->count()
            ]);

            return response()->json(['users' => $users]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch available users', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            return response()->json(['error' => 'Failed to fetch users. Please try again.'], 500);
        }
    }

    private function getAvailableUsers()
    {
        if (Auth::user() instanceof Admin) {
            return User::withCount(['receivedMessages' => function($query) {
                $query->whereNull('read_at');
            }])
            ->orderBy('name')
            ->get();
        } else {
            return Admin::withCount(['receivedMessages' => function($query) {
                $query->whereNull('read_at');
            }])
            ->orderBy('name')
            ->get();
        }
    }
} 