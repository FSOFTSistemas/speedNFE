<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    protected function success($data = null, ?string $message = null, int $status = 200): JsonResponse
    {
        $payload = [];

        if ($message !== null) {
            $payload['message'] = $message;
        }

        if ($data !== null) {
            $payload['data'] = $data;
        }

        return response()->json($payload, $status);
    }

    protected function message(string $message, int $status = 200): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], $status);
    }
}
