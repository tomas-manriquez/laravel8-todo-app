<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Todo;

class TaskController extends Controller
{
    public function index()
    {
        return Inertia::render('Task/Index', [
            'todo' => Todo::all()->map(function ($todo) {
                return [
                    'id' => $todo->id,
                    'name' => $todo->name,
                    'description' => $todo->description,
                    'start_date' => $todo->end_date,
                    'end_date' => $todo->end_date,
                    'priority' => $todo->priority,
                    'is_done' => $todo->is_done,
                    'edit_url' => route('task.edit', $todo->id),
                ];
            }),
            'create_url' => route('task.create'),
        ]
    );
    }
    public function create(Request $request)
    {
        return Inertia::render('Task/Store', [
            'todo' => $request->only(
              'name',
              'description',
              'start_date',
              'end_date',
              'priority',
              'is_done',
            ),'index_url' => route('task.index')
        ]);
    }

}
