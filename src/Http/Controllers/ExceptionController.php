<?php

namespace ExceptionTracker\Http\Controllers;

use ExceptionTracker\Models\ExceptionLog;
use Illuminate\Http\Request;

class ExceptionController
{
    public function index()
    {
        return response()->json([
            'data' => ExceptionLog::latest()->paginate(10)
        ]);
    }

    public function show($id)
    {
        return response()->json(ExceptionLog::findOrFail($id));
    }
}
