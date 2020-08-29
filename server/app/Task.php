<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description', 'completed_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = ['list_id' => 'integer'];

    /**
     * Get the list where the task belongs to
     */
    public function list()
    {
        return $this->belongsTo(TasksList::class);
    }
}
