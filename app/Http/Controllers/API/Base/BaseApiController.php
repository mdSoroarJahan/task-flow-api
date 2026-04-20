<?php

namespace App\Http\Controllers\API\Base;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class BaseApiController extends Controller
{
    protected function success(mixed $data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function error(string $message = 'Something went wrong', int $code = 400, ?\Throwable $e = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        // In development, include exception message
        if (app()->environment('local') && $e) {
            $response['error'] = $e->getMessage();
        }
        return response()->json($response, $code);
    }
}
