<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use App\Models\Project;
use App\Policies\ProjectPolicy;

use App\Models\Task;
use App\Policies\TaskPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Project::class => ProjectPolicy::class,
        Task::class => TaskPolicy::class, // 🔥 QUESTA MANCAVA
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
