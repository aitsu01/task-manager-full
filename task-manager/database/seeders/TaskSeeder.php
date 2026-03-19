<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
  use App\Models\Task;
use App\Models\Project;
use App\Models\User;

class TaskSeeder extends Seeder
{
  

public function run(): void
{
    $projects = Project::all();
    $users = User::all();

    Task::factory(30)->make()->each(function ($task) use ($projects, $users) {
        $task->project_id = $projects->random()->id;
        $task->assigned_to = $users->random()->id;
        $task->save();
    });
}
}
