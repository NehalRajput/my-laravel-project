@extends('Layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">My Tasks</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($tasks as $task)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-2">{{ $task->title }}</h2>
                <p class="text-gray-600 mb-4">{{ Str::limit($task->description, 100) }}</p>
                
                <div class="flex items-center mb-4">
                    <span class="text-sm text-gray-500 mr-4">
                        Due: {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('M d, Y') : 'No due date' }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-sm 
                        @if($task->status === 'completed') bg-green-100 text-green-800
                        @elseif($task->status === 'in_progress') bg-blue-100 text-blue-800
                        @else bg-yellow-100 text-yellow-800
                        @endif">
                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                    </span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">
                        {{ $task->comments->count() }} comments
                    </span>
                    <a href="{{ route('intern.tasks.show', $task) }}" 
                       class="text-blue-600 hover:text-blue-800">
                        View Details →
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection 