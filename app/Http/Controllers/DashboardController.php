<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Task;
use App\Models\Schedule;
use App\Models\User;

class DashboardController extends Controller
{
    public function lead()
    {
        $projects = Project::with(['tasks', 'creator'])->get();
        $tasks = Task::with(['project', 'engineer'])->get();
        $schedules = Schedule::with(['project', 'engineer'])->get();
        $users = User::with('roles')->get();

        // Data untuk chart
        $projectProgressData = $projects->map(function($project) {
            return [
                'name' => substr($project->name, 0, 14),
                'progress' => $project->progress,
            ];
        });

        $statusData = [
            ['name' => 'Assigned', 'value' => $tasks->where('status', 'Assigned')->count(), 'color' => '#3B82F6'],
            ['name' => 'In Progress', 'value' => $tasks->where('status', 'In Progress')->count(), 'color' => '#F59E0B'],
            ['name' => 'Waiting Review', 'value' => $tasks->where('status', 'Waiting Review')->count(), 'color' => '#8B5CF6'],
            ['name' => 'Completed', 'value' => $tasks->where('status', 'Completed')->count(), 'color' => '#10B981'],
        ];

        // ============================================================
        // DATA CHART LOAD PEKERJAAN ENGINEER
        // ============================================================
        $engineers = User::engineers()->active()->get();
        $engineerLoadData = $engineers->map(function($engineer) use ($tasks) {
            $engineerTasks = $tasks->where('engineer_id', $engineer->id);
            $active = $engineerTasks->where('status', '!=', 'Completed')->count();
            $completed = $engineerTasks->where('status', 'Completed')->count();
            return [
                'name' => $engineer->name,
                'active' => $active,
                'completed' => $completed,
                'total' => $active + $completed,
            ];
        })->sortByDesc('total')->values();

        $data = [
            'projectsCount' => $projects->count(),
            'tasksCount' => $tasks->count(),
            'tasksAssigned' => $tasks->where('status', 'Assigned')->count(),
            'tasksInProgress' => $tasks->where('status', 'In Progress')->count(),
            'tasksCompleted' => $tasks->where('status', 'Completed')->count(),
            'upcomingDeadline' => $tasks->where('status', '!=', 'Completed')->sortBy('deadline')->first(),
            'recentProjects' => $projects->take(4),
            'recentTasks' => $tasks->take(4),
            'projectProgressData' => $projectProgressData,
            'statusData' => $statusData,
            'engineerLoadData' => $engineerLoadData, // <-- TAMBAHKAN INI
        ];

        return view('dashboard.lead', $data);
    }

    public function engineer()
    {
        $user = auth()->user();
        $myTasks = Task::with(['project'])->where('engineer_id', $user->id)->get();
        $todaySchedules = Schedule::with(['project'])
            ->where('engineer_id', $user->id)
            ->where('date', now()->toDateString())
            ->get();

        $data = [
            'myTasksCount' => $myTasks->count(),
            'todaySchedulesCount' => $todaySchedules->count(),
            'myTasks' => $myTasks,
            'todaySchedules' => $todaySchedules,
            'avgProgress' => $myTasks->count() ? round($myTasks->avg('progress')) : 0,
            'nearestDeadline' => $myTasks->where('status', '!=', 'Completed')->sortBy('deadline')->first(),
        ];

        return view('dashboard.engineer', $data);
    }
}