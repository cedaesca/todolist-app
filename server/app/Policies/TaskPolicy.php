<?php

namespace App\Policies;

use App\Task;
use App\User;

class TaskPolicy
{
    /**
     * Determine whether the user can view the taksList.
     *
     * @param  App\User  $user
     * @param  App\Task  $task
     * @return bool
     */
    public function view(User $user, Task $task)
    {
        return $task->list->user_id === $user->id;
    }

    /**
     * Determine whether the user can update the task.
     *
     * @param  App\User  $user
     * @param  App\Task  $task
     * @return mixed
     */
    public function update(User $user, Task $task)
    {
        //
    }

    /**
     * Determine whether the user can delete the task.
     *
     * @param  App\User  $user
     * @param  App\Task  $task
     * @return mixed
     */
    public function delete(User $user, Task $task)
    {
        //
    }
}
