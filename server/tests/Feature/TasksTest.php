<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class TasksTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function guests_cannot_hit_tasks_endpoints()
    {
        $list = factory(\App\TasksList::class)->create(['user_id' => 6]);

        $this->post("/lists/{$list->id}/tasks")->assertResponseStatus(401);
        $this->put("/lists/{$list->id}/tasks/1")->assertResponseStatus(401);
        $this->delete("/lists/{$list->id}/tasks/1")->assertResponseStatus(401);
    }

    /** @test */
    public function an_authenticated_user_can_request_all_tasks_from_his_lists()
    {
        $list = factory(\App\TasksList::class)->create(['user_id' => $this->user->id]);
        $tasks = factory(\App\Task::class, 10)->create(['list_id' => $list->id]);

        $this->actingAs($this->user)
            ->get("/lists/{$list->id}/tasks")
            ->assertResponseOk();

        $this->seeJsonEquals($tasks->toArray(), $this->getDecodedResponse());
    }

    /** @test */
    public function an_authenticated_user_cannot_request_tasks_from_other_users_lists()
    {
        $list = factory(\App\TasksList::class)->create(['user_id' => 2]);
        factory(\App\Task::class, 10)->create(['list_id' => $list->id]);

        $this->actingAs($this->user)
            ->get("/lists/{$list->id}/tasks")
            ->assertResponseStatus(403);

        $this->seeJsonDoesntContains($list->tasks->toArray());
    }

    /** @test */
    public function an_authenticated_user_can_create_tasks_in_his_lists()
    {
        $list = factory(\App\TasksList::class)->create(['user_id' => $this->user->id]);

        $taskDescription = 'Create helpful tests';

        $this->actingAs($this->user)
            ->post("/lists/{$list->id}/tasks", ['description' => $taskDescription])
            ->assertResponseStatus(201);

        $this->seeInDatabase('tasks', [
            'list_id' => $list->id,
            'description' => $taskDescription
        ]);

        $list = $list->tasks()->first()->toArray();
        unset($list['completed_at']);

        $this->seeJsonEquals($list, $this->getDecodedResponse());
    }
}
