<?php

namespace ExceptionTracker\Http\Controllers;

use ExceptionTracker\Models\ExceptionLog;
use Illuminate\Http\Request;

class ExceptionController
{
    public function index(Request $request)
    {
        $maxPerPage = (int) config('exception-tracker.max_per_page', 100);
        $perPage = min(
            max(1, (int) $request->query('per_page', 15)),
            $maxPerPage
        );

        return response()->json([
            'data' => ExceptionLog::latest()->paginate($perPage)
        ]);
    }

    public function show($id)
    {
        if (! ctype_digit((string) $id) || (int) $id < 1) {
            abort(400, 'Invalid exception log ID.');
        }

        return response()->json(ExceptionLog::findOrFail((int) $id));
    }
}
