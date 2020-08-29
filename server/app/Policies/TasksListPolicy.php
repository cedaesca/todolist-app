<?php

namespace App\Policies;

use App\User;
use App\TasksList;

class TasksListPolicy
{
    /**
     * Determine whether the user can view the taksList.
     *
     * @param  App\User  $user
     * @param  App\TaksList  $taksList
     * @return bool
     */
    public function view(User $user, TasksList $tasksList)
    {
        return $tasksList->user_id === $user->id;
    }

    /**
     * Determine whether the user can update the taksList.
     *
     * @param  App\User  $user
     * @param  App\TaksList  $taksList
     * @return bool
     */
    public function update(User $user, TasksList $tasksList)
    {
        return $tasksList->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the taksList.
     *
     * @param  App\User  $user
     * @param  App\TaksList  $taksList
     * @return bool
     */
    public function delete(User $user, TasksList $tasksList)
    {
        return $tasksList->user_id === $user->id;
    }
}
