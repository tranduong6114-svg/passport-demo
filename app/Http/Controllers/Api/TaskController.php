<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    private array $tasks = [];

    public function __construct()
    {
        $this->tasks = [
            ['id' => 1, 'title' => 'Học Laravel Passport',  'status' => 'done'],
            ['id' => 2, 'title' => 'Demo với Postman',       'status' => 'in_progress'],
            ['id' => 3, 'title' => 'Viết tài liệu kỹ thuật', 'status' => 'pending'],
        ];
    }

    public function index(Request $request)
    {
        if (! $request->user()->tokenCan('read-tasks')) {
            return response()->json([
                'message' => 'Token không có quyền đọc tasks',
            ], 403);
        }

        return response()->json([
            'data'  => $this->tasks,
            'count' => count($this->tasks),
        ]);
    }

    public function show(Request $request, $id)
    {
        $task = collect($this->tasks)->firstWhere('id', (int) $id);

        if (! $task) {
            return response()->json(['message' => 'Task không tồn tại'], 404);
        }

        return response()->json(['data' => $task]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'  => 'required|string|max:255',
            'status' => 'required|in:pending,in_progress,done',
        ]);

        $newTask = [
            'id'     => count($this->tasks) + 1,
            'title'  => $validated['title'],
            'status' => $validated['status'],
        ];

        return response()->json([
            'message' => 'Tạo task thành công',
            'data'    => $newTask,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title'  => 'sometimes|string|max:255',
            'status' => 'sometimes|in:pending,in_progress,done',
        ]);

        return response()->json([
            'message' => "Cập nhật task ID=$id thành công",
            'data'    => $validated,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        return response()->json([
            'message' => "Xóa task ID=$id thành công",
        ]);
    }
}