<?php

namespace ExceptionTracker\Http\Controllers;

use ExceptionTracker\Models\ExceptionLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExceptionController
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => ExceptionLog::latest()->paginate(10)
        ]);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(ExceptionLog::findOrFail($id));
    }
}
