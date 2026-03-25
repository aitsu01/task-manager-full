<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'creator_id',
        'deadline'

    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function users()
{
    return $this->belongsToMany(User::class)
        ->withPivot('role')
        ->withTimestamps();
}



public function creator()
{
    return $this->belongsTo(User::class, 'creator_id');
}




}