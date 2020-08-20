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
}
