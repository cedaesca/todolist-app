<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class TasksTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function guests_cannot_hit_tasks_endpoints()
    {
        $list = factory(\App\TasksList::class)->create(['user_id' => 6]);

        $this->post("/tasks")->assertResponseStatus(401);
        $this->put("/tasks/1")->assertResponseStatus(401);
        $this->delete("/tasks/1")->assertResponseStatus(401);
    }

    /** @test */
    public function an_authenticated_user_can_request_all_tasks_from_his_lists()
    {
        factory(\App\TasksList::class, 5)
            ->create(['user_id' => $this->user->id])
            ->each(function ($list) {
                factory(\App\Task::class, 15)->create(['list_id' => $list->id]);
            });

        $listsWithTasks = $this->user->lists()->with('tasks')->get()->toArray();

        $this->actingAs($this->user)
            ->get("/lists?withTasks=1")
            ->assertResponseOk();

        $this->seeJsonEquals($listsWithTasks, $this->getDecodedResponse());
    }

    /** @test */
    public function an_authenticated_user_cannot_request_tasks_from_other_users_lists()
    {
        factory(\App\TasksList::class, 5)
            ->create(['user_id' => 2])
            ->each(function ($list) {
                factory(\App\Task::class, 15)->create(['list_id' => $list->id]);
            });

        $randomUser = \App\User::find(2);

        $listsWithTasks = $randomUser->lists()->with('tasks')->get()->toArray();

        $this->actingAs($this->user)
            ->get("/lists?withTasks=1");

        $this->seeJsonDoesntContains($listsWithTasks);
    }

    /** @test */
    public function an_authenticated_user_can_create_tasks_in_his_lists()
    {
        $list = factory(\App\TasksList::class)->create(['user_id' => $this->user->id]);

        $taskData = [
            'list_id' => $list->id,
            'description' => 'Create helpful tests'
        ];

        $this->actingAs($this->user)
            ->post("/tasks", $taskData)
            ->assertResponseStatus(201);

        $this->seeInDatabase('tasks', $taskData);

        $list = $list->tasks()->first()->toArray();
        unset($list['completed_at']);

        $this->seeJsonEquals($list, $this->getDecodedResponse());
    }

    /** @test */
    public function an_authenticated_user_cannot_create_tasks_in_another_user_list()
    {
        $randomUser = \App\User::find(1);

        $list = factory(\App\TasksList::class)->create(['user_id' => $randomUser->id]);

        $taskDescription = 'Create helpful tests';

        $this->actingAs($this->user)
            ->post("/lists/{$list->id}/tasks", ['description' => $taskDescription])
            ->assertResponseStatus(404);

        $this->notSeeInDatabase('tasks', ['description' => $taskDescription]);
    }

    /** @test */
    public function an_authenticated_user_can_request_a_single_task()
    {
        $task = factory(\App\Task::class)->create(['list_id' => function () {
            $list = factory(\App\TasksList::class)->create(['user_id' => $this->user->id]);

            return $list->id;
        }]);

        $this->actingAs($this->user)
            ->get("tasks/{$task->id}")
            ->assertResponseOk();

        $this->seeJsonEquals($task->toArray(), $this->getDecodedResponse());
    }

    /** @test */
    public function an_authenticated_user_cannot_request_tasks_from_other_user()
    {
        $task = factory(\App\Task::class)->create(['list_id' => function () {
            $list = factory(\App\TasksList::class)->create(['user_id' => 2]);

            return $list->id;
        }]);

        $this->actingAs($this->user)
            ->get("tasks/{$task->id}")
            ->assertResponseStatus(403);
    }

    /** @test */
    public function an_authenticated_user_can_update_a_single_task()
    {
        $task = factory(\App\Task::class)->create(['list_id' => function () {
            $list = factory(\App\TasksList::class)->create(['user_id' => $this->user->id]);

            return $list->id;
        }, 'completed_at' => null]);

        $taskData = [
            'description' => 'Updated description yay!',
            'completed' => true
        ];

        $this->actingAs($this->user)
            ->put("tasks/{$task->id}", $taskData)
            ->assertResponseOk();

        $this->seeInDatabase('tasks', [
            'description' => $taskData['description']
        ]);
    }

    /** @test */
    public function an_authenticated_user_cannot_update_a_task_from_other_users()
    {
        $task = factory(\App\Task::class)->create(['list_id' => function () {
            $list = factory(\App\TasksList::class)->create(['user_id' => 2]);

            return $list->id;
        }, 'completed_at' => null]);

        $taskData = [
            'description' => 'Updated description yay!',
            'completed' => true
        ];

        $this->actingAs($this->user)
            ->put("tasks/{$task->id}", $taskData)
            ->assertResponseStatus(403);

        $this->seeInDatabase('tasks', [
            'description' => $task->description,
            'list_id' => $task->list_id
        ]);
    }

    /** @test */
    public function an_authenticated_user_can_delete_a_task()
    {
        $task = factory(\App\Task::class)->create(['list_id' => function () {
            $list = factory(\App\TasksList::class)->create(['user_id' => $this->user->id]);

            return $list->id;
        }]);

        $this->seeInDatabase('tasks', [
            'description' => $task->description,
            'list_id' => $task->list_id
        ]);

        $this->actingAs($this->user)
            ->delete("/tasks/{$task->id}")
            ->assertResponseOk();

        $this->notSeeInDatabase('tasks', [
            'description' => $task->description,
            'list_id' => $task->list_id
        ]);

        $this->seeJsonEquals($task->toArray(), $this->getDecodedResponse());
    }
}
