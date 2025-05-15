@extends('Layouts.app')

@section('content')
<div class="container">
    <h2>My Tasks</h2>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="task-list">
        @forelse($tasks as $task)
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">{{ $task->title }}</h5>
                    <p class="card-text">{{ $task->description }}</p>
                    <div class="task-status">
                        <form action="{{ route('intern.tasks.update-status', $task) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <select name="status" onchange="this.form.submit()" class="form-select">
                                <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="todo" {{ $task->status === 'todo' ? 'selected' : '' }}>To Do</option>
                                <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info">No tasks assigned yet.</div>
        @endforelse
    </div>
</div>
@endsection 