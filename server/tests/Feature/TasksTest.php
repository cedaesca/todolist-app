<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class TasksTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function guests_cannot_hit_tasks_endpoints()
    {
        $this->get('/tasks')->assertResponseStatus(401);
        $this->post('/tasks')->assertResponseStatus(401);
        $this->put('/tasks/1')->assertResponseStatus(401);
        $this->delete('/tasks/1')->assertResponseStatus(401);
    }
}
