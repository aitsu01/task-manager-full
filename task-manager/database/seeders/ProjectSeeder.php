<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\User;

class ProjectSeeder extends Seeder
{


public function run(): void
{
    $users = User::all();

    Project::factory(8)->make()->each(function ($project) use ($users) {
        $project->user_id = $users->random()->id;
        $project->save();
    });
}
}
