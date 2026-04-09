<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->get('filter', 'active');
        $categoryId = $request->get('category');

        $query = Task::with('category')->orderBy('sort_order');

        if ($filter === 'active') {
            $query->incomplete();
        } elseif ($filter === 'completed') {
            $query->where('completed', true);
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $tasks = $query->get()->groupBy('quadrant');
        $categories = Category::orderBy('name')->get();

        $stats = [
            'total' => Task::count(),
            'completed' => Task::where('completed', true)->count(),
            'overdue' => Task::incomplete()->whereNotNull('due_date')->where('due_date', '<', now()->toDateString())->count(),
            'today' => Task::incomplete()->whereDate('due_date', now()->toDateString())->count(),
        ];

        return view('tasks.index', compact('tasks', 'categories', 'filter', 'stats'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'quadrant' => 'required|in:do_first,schedule,delegate,eliminate',
            'category_id' => 'nullable|exists:categories,id',
            'due_date' => 'nullable|date',
        ]);

        $validated['sort_order'] = Task::where('quadrant', $validated['quadrant'])->max('sort_order') + 1;

        $task = Task::create($validated);
        $task->load('category');

        return response()->json($task, 201);
    }

    public function update(Request $request, Task $task): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'quadrant' => 'sometimes|required|in:do_first,schedule,delegate,eliminate',
            'category_id' => 'nullable|exists:categories,id',
            'due_date' => 'nullable|date',
            'completed' => 'sometimes|boolean',
            'sort_order' => 'sometimes|integer',
        ]);

        if (isset($validated['completed']) && $validated['completed'] && !$task->completed) {
            $validated['completed_at'] = now();
        } elseif (isset($validated['completed']) && !$validated['completed']) {
            $validated['completed_at'] = null;
        }

        $task->update($validated);
        $task->load('category');

        return response()->json($task);
    }

    public function destroy(Task $task): JsonResponse
    {
        $task->delete();
        return response()->json(null, 204);
    }

    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tasks' => 'required|array',
            'tasks.*.id' => 'required|exists:tasks,id',
            'tasks.*.quadrant' => 'required|in:do_first,schedule,delegate,eliminate',
            'tasks.*.sort_order' => 'required|integer',
        ]);

        foreach ($validated['tasks'] as $taskData) {
            Task::where('id', $taskData['id'])->update([
                'quadrant' => $taskData['quadrant'],
                'sort_order' => $taskData['sort_order'],
            ]);
        }

        return response()->json(['message' => 'Reordered']);
    }

    public function stats(): JsonResponse
    {
        $stats = [
            'total' => Task::count(),
            'completed' => Task::where('completed', true)->count(),
            'overdue' => Task::incomplete()->whereNotNull('due_date')->where('due_date', '<', now()->toDateString())->count(),
            'today' => Task::incomplete()->whereDate('due_date', now()->toDateString())->count(),
            'by_quadrant' => [
                'do_first' => Task::incomplete()->where('quadrant', 'do_first')->count(),
                'schedule' => Task::incomplete()->where('quadrant', 'schedule')->count(),
                'delegate' => Task::incomplete()->where('quadrant', 'delegate')->count(),
                'eliminate' => Task::incomplete()->where('quadrant', 'eliminate')->count(),
            ],
        ];

        return response()->json($stats);
    }
}
