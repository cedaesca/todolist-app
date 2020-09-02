<?php

namespace App\Events;

use App\User;

class UserCreated extends Event
{
    /**
     * Created User Instance
     * 
     * @var \App\User
     */
    public $user;

    /**
     * Create a new event instance.
     * 
     * @param \App\User $user
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
