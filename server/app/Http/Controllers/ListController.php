<?php

namespace App\Http\Controllers;

use App\TasksList;
use Illuminate\Http\Request;

class ListController extends Controller
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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            'name' => 'required|string|min:3'
        ]);

        $list = $request->user()->lists()->create($request->all());

        return response()->json($list, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TasksList  $tasksList
     * @return \Illuminate\Http\Response
     */
    public function show(TasksList $tasksList)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TasksList  $tasksList
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TasksList $tasksList)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TasksList  $tasksList
     * @return \Illuminate\Http\Response
     */
    public function destroy(TasksList $tasksList)
    {
        //
    }
}
