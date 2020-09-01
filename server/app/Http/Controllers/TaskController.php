<?php

namespace App\Http\Controllers;

use App\Task;
use App\TasksList;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TaskController extends Controller
{
    /**
     * Instantiate a new UserController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @param int $list
     * @return \Illuminate\Http\Response
     */
    public function index(int $list)
    {
        $list = TasksList::findOrFail($list);

        $this->authorize('view', $list);

        return response()->json($list->tasks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'list_id' => 'required|int|min:1',
            'description' => 'required|string|min:3'
        ]);

        $list = TasksList::findOrFail($request->list_id);

        $this->authorize('create-task', $list);

        $task = $list->tasks()->create($request->all());

        return response()->json($task, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $task
     * @return \Illuminate\Http\Response
     */
    public function show(int $task)
    {
        // We eager load the list so the policy
        // can make use of that information
        // without querying again
        $task = Task::with('list')->findOrFail($task);

        $this->authorize('view', $task);

        $task->makeHidden('list');

        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $task
     * @return \Illuminate\Http\Response
     */
    public function update(int $task, Request $request)
    {
        $this->validate($request, [
            'description' => 'string|min:3',
            'completed' => 'bool'
        ]);

        // We eager load the list so the policy
        // can make use of that information
        // without querying again
        $task = Task::with('list')->findOrFail($task);

        $this->authorize('update', $task);

        if ($request->completed) {
            $request->completed_at = Carbon::now()->format('Y-m-d H:m:s');
        }

        $task->description = $request->description ?? $task->description;

        $task->save();

        return response()->json($task);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $task)
    {
        // We eager load the list so the policy
        // can make use of that information
        // without querying again
        $task = Task::with('list')->findOrFail($task);

        $this->authorize('delete', $task);

        $task->makeHidden('list');

        $task->delete();

        return response()->json($task, 200);
    }
}
