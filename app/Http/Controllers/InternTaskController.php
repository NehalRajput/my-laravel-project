<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InternTaskController extends Controller
{
    /**
     * Display a listing of the intern's tasks
     */
    public function index()
    {
        $intern = Auth::user();
        $tasks = $intern->assignedTasks()
            ->with('comments')
            ->select('tasks.id', 'tasks.title', 'tasks.description', 'tasks.status', 'tasks.due_date')
            ->get();

        return view('intern.tasks.index', compact('tasks'));
    }

    /**
     * Display the specified task
     */
    public function show(Task $task)
    {
        // Check if the intern is assigned to this task
        $intern = Auth::user();
        if (!$task->interns->contains($intern->id)) {
            abort(403, 'Unauthorized action.');
        }

        $task->load('comments');
        return view('intern.tasks.show', compact('task'));
    }

    /**
     * Add a comment to the task
     */
    public function addComment(Request $request, Task $task)
    {
        // Validate the request
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        // Check if the intern is assigned to this task
        $intern = Auth::user();
        if (!$task->interns->contains($intern->id)) {
            abort(403, 'Unauthorized action.');
        }

        // Create the comment
        $comment = new Comment([
            'content' => $request->content,
            'user_id' => $intern->id,
            'task_id' => $task->id
        ]);
        $comment->save();

        return redirect()->back()->with('success', 'Comment added successfully');
    }
} 
