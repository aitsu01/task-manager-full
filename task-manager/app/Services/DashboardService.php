<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;

class DashboardService
{
    public function getDashboardData($user)
    {
        $isAdmin = $user->role && $user->role->name === 'admin';

        // PROJECT QUERY
        $projectQuery = Project::query();

        if (!$isAdmin) {
            $projectQuery->where('user_id', $user->id);
        }

        $totalProjects = $projectQuery->count();

        // TASK BASE QUERY
        $baseTaskQuery = Task::query();

        if (!$isAdmin) {
            $baseTaskQuery->whereHas('project', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $totalTasks = (clone $baseTaskQuery)->count();

        $doneTasks = (clone $baseTaskQuery)
            ->where('status', 'done')
            ->count();

        $completionRate = $totalTasks > 0
            ? round(($doneTasks / $totalTasks) * 100, 1)
            : 0;

        $tasksPerStatusRaw = (clone $baseTaskQuery)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $tasksPerStatus = [];

        foreach (['todo', 'doing', 'done'] as $status) {
            $count = $tasksPerStatusRaw[$status] ?? 0;
            $percentage = $totalTasks > 0
                ? round(($count / $totalTasks) * 100, 1)
                : 0;

            $tasksPerStatus[$status] = [
                'count' => $count,
                'percentage' => $percentage
            ];
        }

        $overdueTasks = (clone $baseTaskQuery)
            ->whereDate('due_date', '<', now())
            ->where('status', '!=', 'done')
            ->count();

        $recentTasks = (clone $baseTaskQuery)
            ->latest()
            ->limit(5)
            ->get(['id', 'title', 'status', 'due_date']);


        // WEEKLY COMPLETED (ultimi 7 giorni)
$weeklyCompletedRaw = (clone $baseTaskQuery)
    ->whereNotNull('completed_at')
    ->whereDate('completed_at', '>=', now()->subDays(6))
    ->selectRaw('DATE(completed_at) as date, COUNT(*) as completed')
    ->groupBy('date')
    ->pluck('completed', 'date');

$weeklyCompleted = [];

for ($i = 6; $i >= 0; $i--) {
    $date = now()->subDays($i)->format('Y-m-d');

    $weeklyCompleted[] = [
        'date' => $date,
        'completed' => $weeklyCompletedRaw[$date] ?? 0
    ];
}

        return [
            'total_projects' => $totalProjects,
            'total_tasks' => $totalTasks,
            'completion_rate' => $completionRate,
            'tasks_per_status' => $tasksPerStatus,
            'overdue_tasks' => $overdueTasks,
            'recent_tasks' => $recentTasks,
            'weekly_completed' => $weeklyCompleted,
        ];
    }
}
