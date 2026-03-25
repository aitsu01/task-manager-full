<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\TaskResource;
use App\Models\Task;


class MyTaskController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $tasks = Task::where('assigned_user_id', $user->id)
            ->with(['project', 'assignedUser'])
            ->latest()
            ->paginate(10);

        return TaskResource::collection($tasks);
    }
}