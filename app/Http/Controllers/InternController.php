<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\InternRequest;                                                                                                
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class InternController extends Controller
{
    public function tasks()
    {
        try {
            $tasks = Auth::user()->tasks()
                ->with(['comments', 'interns'])
                ->latest()
                ->get();

            Log::info('Tasks fetched successfully for intern', [
                'user_id' => Auth::id(),
                'task_count' => $tasks->count()
            ]);

            return view('intern.tasks', compact('tasks'));
        } catch (\Exception $e) {
            Log::error('Failed to fetch tasks for intern', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Failed to load tasks. Please try again later.');
        }
    }

    public function updateTaskStatus(InternRequest $request, Task $task)
    {
        try {
            DB::beginTransaction();

            if (!$task->interns->contains(Auth::id())) {
                throw new \Exception('Unauthorized to update this task');
            }

            $task->update(['status' => $request->status]);

            DB::commit();

            Log::info('Task status updated successfully', [
                'task_id' => $task->id,
                'user_id' => Auth::id(),
                'new_status' => $request->status
            ]);

            return redirect()->back()->with('success', 'Task status updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update task status', [
                'error' => $e->getMessage(),
                'task_id' => $task->id,
                'user_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Failed to update task status. Please try again later.');
        }
    }
}
