<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Task;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->query('q', ''));
        $user = auth()->user();
        $isLead = $user->hasRole('Lead Engineer');

        $projects = collect();
        $tasks = collect();

        if ($q !== '') {
            $projectQuery = Project::query()
                ->where(function ($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%")
                          ->orWhere('client', 'like', "%{$q}%");
                });

            $taskQuery = Task::with(['project', 'engineer'])
                ->where('title', 'like', "%{$q}%");

            // Engineer cuma boleh lihat task miliknya sendiri
            if (! $isLead) {
                $taskQuery->where('engineer_id', $user->id);
            }

            $projects = $isLead ? $projectQuery->get() : collect();
            $tasks = $taskQuery->get();
        }

        return view('search.index', [
            'q' => $q,
            'projects' => $projects,
            'tasks' => $tasks,
        ]);
    }
}