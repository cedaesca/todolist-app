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
     * Get the User that owns the list
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
