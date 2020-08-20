<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TasksList extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = ['user_id' => 'integer'];

    /**
     * Get the User that owns the list
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tasks belonging to the list
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'list_id');
    }
}
