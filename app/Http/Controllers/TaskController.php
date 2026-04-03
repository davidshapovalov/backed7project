<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Note;
use App\Models\Task;

class TaskController extends Controller
{
    /**
     * Display all tasks for a given note.
     */
    public function index(Note $note)
    {
        $tasks = $note->tasks()->with('comments')->get();

        return response()->json(['tasks' => $tasks], Response::HTTP_OK);
    }

    /**
     * Store a newly created task for a note.
     */
    public function store(Request $request, Note $note)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'is_done' => ['sometimes', 'boolean'],
            'due_at' => ['nullable', 'date'],
        ]);

        $task = $note->tasks()->create($validated);

        return response()->json([
            'message' => 'Task created successfully.',
            'task' => $task,
        ], Response::HTTP_CREATED);
    }

    /**
     * Display a specific task of a note.
     */
    public function show(Note $note, Task $task)
    {
        // Проверяем, что задача принадлежит заметке
        if ($task->note_id !== $note->id) {
            return response()->json(['message' => 'Task not found for this note.'], Response::HTTP_NOT_FOUND);
        }

        $task->load('comments');

        return response()->json(['task' => $task], Response::HTTP_OK);
    }

    /**
     * Update a specific task.
     */
    public function update(Request $request, Note $note, Task $task)
    {
        if ($task->note_id !== $note->id) {
            return response()->json(['message' => 'Task not found for this note.'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'is_done' => ['sometimes', 'boolean'],
            'due_at' => ['nullable', 'date'],
        ]);

        $task->update($validated);

        return response()->json([
            'message' => 'Task updated successfully.',
            'task' => $task,
        ], Response::HTTP_OK);
    }

    /**
     * Remove a specific task.
     */
    public function destroy(Note $note, Task $task)
    {
        if ($task->note_id !== $note->id) {
            return response()->json(['message' => 'Task not found for this note.'], Response::HTTP_NOT_FOUND);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted successfully.'], Response::HTTP_OK);
    }
}
