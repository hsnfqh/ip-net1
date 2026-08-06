<?php
// app/Http/Controllers/ProjectController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Http\Requests\ProjectRequest;

class ProjectController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isLead = $user->hasRole('Lead Engineer');

        if ($isLead) {
            $projects = Project::with(['creator', 'tasks'])->get();
        } else {
            // Engineer hanya bisa lihat project yang dia punya task di dalamnya
            $projectIds = \App\Models\Task::where('engineer_id', $user->id)
                ->pluck('project_id')
                ->unique();
            $projects = Project::with(['creator', 'tasks'])
                ->whereIn('id', $projectIds)
                ->get();
        }

        return view('projects.index', compact('projects', 'isLead'));
    }

    public function store(ProjectRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $project = Project::create($data);

        if ($request->wantsJson() || $request->isJson() || $request->ajax()) {
            return response()->json($project->load(['creator', 'tasks']), 201);
        }

        return redirect()->route('projects.index')
            ->with('success', 'Project berhasil ditambahkan!');
    }

    public function update(ProjectRequest $request, Project $project)
    {
        $project->update($request->validated());

        if ($request->wantsJson() || $request->isJson() || $request->ajax()) {
            return response()->json($project->load(['creator', 'tasks']));
        }

        return redirect()->route('projects.index')
            ->with('success', 'Project berhasil diperbarui!');
    }

    public function destroy(Project $project)
    {
        try {
            $project->delete();

            if (request()->wantsJson() || request()->isJson() || request()->ajax()) {
                return response()->json(['message' => 'Project berhasil dihapus!'], 200);
            }

            return redirect()->route('projects.index')
                ->with('success', 'Project berhasil dihapus!');
        } catch (\Exception $e) {
            if (request()->wantsJson() || request()->isJson() || request()->ajax()) {
                return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
            }
            return redirect()->route('projects.index')
                ->with('error', 'Gagal menghapus project: ' . $e->getMessage());
        }
    }

    public function show(Project $project)
    {
        return response()->json($project->load(['tasks', 'creator']));
    }

    public function getData()
    {
        $projects = Project::with(['tasks'])->get()->map(function($project) {
            return [
                'id' => $project->id,
                'name' => $project->name,
                'client' => $project->client,
                'location' => $project->location,
                'deadline' => $project->deadline->format('Y-m-d'),
                'status' => $project->status,
                'progress' => $project->progress,
            ];
        });

        return response()->json($projects);
    }
}