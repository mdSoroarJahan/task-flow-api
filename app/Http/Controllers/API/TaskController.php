<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Base\BaseApiController;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\Task\TaskResource;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Gate;

class TaskController extends BaseApiController
{
    public function index(): JsonResponse
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            $tasks = $user->tasks()->latest()->paginate(10);

            return $this->success(TaskResource::collection($tasks), 'Tasks fetched successfully', code: 200);
        } catch (\Throwable $e) {
            return $this->error('Failed to fetch tasks', 500, $e);
        }
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            $task = $user->tasks()->create($request->validated());
            return $this->success(new TaskResource($task), 'Task created successfully', 201);
        } catch (\Throwable $e) {
            return $this->error('Failed to create task', 500, $e);
        }
    }

    public function show(Task $task): JsonResponse
    {
        try {
            Gate::authorize('view', $task);
            return $this->success(new TaskResource($task), 'Task details retrieve successfully');
        } catch (\Throwable $e) {
            return $this->error('Failed to retrieve task', 500, $e);
        }
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        try {
            Gate::authorize('update', $task);
            $task->update($request->validated());
            return $this->success(new TaskResource($task), 'Task updated successfully');
        } catch (\Throwable $e) {
            return $this->error('Failed to update task', 500, $e);
        }
    }

    public function destroy(Task $task): JsonResponse
    {
        try {
            Gate::authorize('delete', $task);
            $task->delete();
            return $this->success(null, 'Task soft-deleted');
        } catch (\Throwable $e) {
            return $this->error('Failed to delete task', 500, $e);
        }
    }

    public function restore($id): JsonResponse
    {
        try {
            $task = Task::withTrashed()->findOrFail($id);
            Gate::authorize('restore', $task);
            $task->restore();
            return $this->success(new TaskResource($task), 'task restored successfully');
        } catch (\Throwable $e) {
            return $this->error('Failed to restore task', 500, $e);
        }
    }
}
