<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Base\BaseApiController;
use App\Http\Resources\Task\TaskResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

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
}
