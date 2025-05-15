@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('intern.tasks.index') }}" class="text-blue-600 hover:text-blue-800">
            ← Back to Tasks
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h1 class="text-2xl font-bold mb-4">{{ $task->title }}</h1>
        
        <div class="flex items-center mb-6">
            <span class="px-3 py-1 rounded-full text-sm mr-4
                @if($task->status === 'completed') bg-green-100 text-green-800
                @elseif($task->status === 'in_progress') bg-blue-100 text-blue-800
                @else bg-yellow-100 text-yellow-800
                @endif">
                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
            </span>
            <span class="text-sm text-gray-500">
                Due: {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('M d, Y') : 'No due date' }}
            </span>
        </div>

        <div class="prose max-w-none mb-8">
            {{ $task->description }}
        </div>

        <!-- Comments Section -->
        <div class="border-t pt-6">
            <h2 class="text-xl font-semibold mb-4">Comments</h2>

            <!-- Add Comment Form -->
            <form action="{{ route('intern.tasks.comment', $task) }}" method="POST" class="mb-6">
                @csrf
                <div class="mb-4">
                    <textarea name="content" rows="3" 
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Add a comment..."></textarea>
                    @error('content')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" 
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Add Comment
                </button>
            </form>

            <!-- Comments List -->
            <div class="space-y-4">
                @foreach($task->comments->sortByDesc('created_at') as $comment)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <span class="font-medium">{{ $comment->user->name }}</span>
                            <span class="text-gray-500 text-sm ml-2">
                                {{ $comment->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-gray-700">{{ $comment->content }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection 