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
    public function index(Request $request)
    {
        return response()->json($request->user()->lists);
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
     * @param  int $list
     * @return \Illuminate\Http\Response
     */
    public function show(int $list)
    {
        $list = TasksList::findOrFail($list);

        $this->authorize('view', $list);

        return response()->json($list);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $list
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(int $list, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|min:3'
        ]);

        $list = TasksList::findOrFail($list);

        $this->authorize('update', $list);

        $list->name = $request->name ?? $list->name;
        $list->save();

        return response()->json($list);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $tasksList
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $list)
    {
        $list = TasksList::findOrFail($list);

        $this->authorize('delete', $list);

        $list->delete();

        return response()->json($list);
    }
}
