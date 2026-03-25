<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'due_date',
        'project_id',
        'assigned_to'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedUser()
{
    return $this->belongsTo(\App\Models\User::class, 'assigned_user_id');
}
}
