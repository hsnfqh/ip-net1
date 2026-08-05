<?php
// app/Http/Controllers/TaskController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Http\Requests\TaskRequest;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with(['project', 'engineer', 'creator'])->get();
        $projects = Project::all();
        $engineers = User::engineers()->get();
        
        return view('tasks.index', compact('tasks', 'projects', 'engineers'));
    }

    public function store(TaskRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $data['progress'] = 0;
        $data['attachments'] = 0;
        $data['status'] = $data['status'] ?? 'Assigned';
        
        $task = Task::create($data);
        $task = $task->load(['project', 'engineer', 'creator']);

        if ($request->wantsJson() || $request->isJson() || $request->ajax()) {
            return response()->json($task, 201);
        }

        return redirect()->route('tasks.index')
            ->with('success', 'Task berhasil dibuat dan diassign!');
    }

    public function update(Request $request, Task $task)
    {
        $user = auth()->user();

        // Lead Engineer bisa mengubah seluruh field task (edit penuh).
        // Engineer hanya boleh mengubah status & progress miliknya sendiri.
        if ($user->hasRole('Lead Engineer')) {
            $validated = $request->validate([
                'title'       => 'sometimes|required|string|max:255',
                'project_id'  => 'sometimes|required|exists:projects,id',
                'engineer_id' => 'sometimes|required|exists:users,id',
                'priority'    => 'sometimes|required|in:High,Medium,Low',
                'deadline'    => 'sometimes|required|date',
                'description' => 'sometimes|nullable|string',
                'status'      => 'sometimes|required|in:Assigned,In Progress,Waiting Review,Completed',
                'progress'    => 'sometimes|integer|min:0|max:100',
            ]);
        } else {
            $validated = $request->validate([
                'status'   => 'nullable|in:Assigned,In Progress,Waiting Review,Completed',
                'progress' => 'nullable|integer|min:0|max:100',
            ]);

            // Engineer hanya boleh update task miliknya sendiri
            abort_unless($task->engineer_id === $user->id, 403);
        }

        if (empty($validated)) {
            return response()->json(['message' => 'Tidak ada data yang diupdate'], 422);
        }

        $task->fill($validated);

        if (($validated['status'] ?? null) === 'Completed') {
            $task->progress = 100;
        }

        $task->save();
        $task = $task->load(['project', 'engineer', 'creator']);

        if ($request->wantsJson() || $request->isJson() || $request->ajax()) {
            return response()->json($task);
        }

        return redirect()->route('tasks.index')
            ->with('success', 'Task berhasil diperbarui!');
    }

    public function destroy(Task $task)
    {
        $task->delete();

        if (request()->wantsJson() || request()->isJson() || request()->ajax()) {
            return response()->json(['message' => 'Task berhasil dihapus!']);
        }

        return redirect()->route('tasks.index')
            ->with('success', 'Task berhasil dihapus!');
    }

    public function getKanbanData()
    {
        $user = auth()->user();
        
        $tasks = Task::with(['project', 'engineer'])
            ->when(!$user->hasRole('Lead Engineer'), function($query) use ($user) {
                return $query->where('engineer_id', $user->id);
            })
            ->get();

        $columns = ['Assigned', 'In Progress', 'Waiting Review', 'Completed'];
        $data = [];

        foreach ($columns as $column) {
            $data[$column] = $tasks->where('status', $column)->values();
        }

        return response()->json($data);
    }
}