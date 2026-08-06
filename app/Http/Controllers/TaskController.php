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
        $user = auth()->user();
        $isLead = $user->hasRole('Lead Engineer');
        $tasks = Task::with(['project', 'engineer', 'creator'])
            ->when(!$isLead, function($query) use ($user) {
                return $query->where('engineer_id', $user->id);
            })
            ->get();
        $projects = Project::all();
        $engineers = $isLead ? User::engineers()->get() : collect([$user]);
        $currentUserId = $user->id;

        return view('tasks.index', compact('tasks', 'projects', 'engineers', 'currentUserId', 'isLead'));
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

        // Kirim notifikasi ke engineer yang ditunjuk
        if ($task->engineer_id) {
            \App\Models\Notification::create([
                'user_id' => $task->engineer_id,
                'title' => 'Tugas Baru Ditugaskan',
                'message' => 'Anda ditugaskan pada tugas baru: "' . $task->title . '" untuk proyek ' . ($task->project?->name ?? 'Project'),
                'url' => route('tasks.index'),
                'is_read' => false,
            ]);
        }

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
                'progress'    => 'sometimes|nullable|integer|min:0|max:100',
                'doc_file'    => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
            ]);
        } else {
            $validated = $request->validate([
                'status'   => 'nullable|in:Assigned,In Progress,Waiting Review,Completed',
                'progress' => 'nullable|integer|min:0|max:100',
                'doc_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
            ]);

            // Engineer hanya boleh update task miliknya sendiri
            abort_unless($task->engineer_id === $user->id, 403);
        }

        // Handle file upload dokumentasi
        if ($request->hasFile('doc_file')) {
            if ($task->doc_file && Storage::disk('public')->exists($task->doc_file)) {
                Storage::disk('public')->delete($task->doc_file);
            }
            $path = $request->file('doc_file')->store('task-docs', 'public');
            $task->doc_file = $path;
            $task->attachments = ($task->attachments ?? 0) + 1;
        }

        // Fill non-doc_file validated fields
        $fieldsToFill = array_filter($validated, fn($v, $k) => $k !== 'doc_file' && $v !== null, ARRAY_FILTER_USE_BOTH);
        $task->fill($fieldsToFill);

        // Auto progress defaults if status changed without explicit progress slider update
        if (isset($validated['status'])) {
            if ($validated['status'] === 'Completed') {
                $task->progress = 100;
            } elseif ($validated['status'] === 'In Progress' && (!isset($validated['progress']) || $validated['progress'] === 0)) {
                $task->progress = max($task->progress, 50);
            } elseif ($validated['status'] === 'Waiting Review' && (!isset($validated['progress']) || $validated['progress'] === 0)) {
                $task->progress = max($task->progress, 90);
            }
        }

        // Cek perubahan field sebelum disave
        $statusChanged = $task->isDirty('status');
        $progressChanged = $task->isDirty('progress');
        $engineerChanged = $task->isDirty('engineer_id');

        $task->save();
        $task = $task->load(['project', 'engineer', 'creator']);

        // Kirim notifikasi jika engineer berubah
        if ($engineerChanged && $task->engineer_id) {
            \App\Models\Notification::create([
                'user_id' => $task->engineer_id,
                'title' => 'Tugas Baru Ditugaskan',
                'message' => 'Anda ditugaskan pada tugas: "' . $task->title . '" untuk proyek ' . ($task->project?->name ?? 'Project'),
                'url' => route('tasks.index'),
                'is_read' => false,
            ]);
        }

        // Kirim notifikasi ke Lead jika diupdate oleh engineer biasa dan ada progres/status selesai
        $user = auth()->user();
        if ($user && !$user->hasRole('Lead Engineer')) {
            if (($statusChanged && in_array($task->status, ['Waiting Review', 'Completed'])) || 
                ($progressChanged && $task->progress == 100)) {
                
                $leads = \App\Models\User::leadEngineer()->get();
                $notificationRecipients = $leads->pluck('id')->push($task->created_by)->unique();
                
                foreach ($notificationRecipients as $recipientId) {
                    if ($recipientId != $user->id) {
                        \App\Models\Notification::create([
                            'user_id' => $recipientId,
                            'title' => 'Pekerjaan Diperbarui oleh Engineer',
                            'message' => 'Engineer ' . $user->name . ' telah mengubah status "' . $task->title . '" menjadi ' . $task->status . ' (Progress: ' . $task->progress . '%).',
                            'url' => route('tasks.index'),
                            'is_read' => false,
                        ]);
                    }
                }
            }
        }

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