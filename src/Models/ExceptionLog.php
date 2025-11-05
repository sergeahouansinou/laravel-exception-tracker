<?php

namespace ExceptionTracker\Models;

use Illuminate\Database\Eloquent\Model;

class ExceptionLog extends Model
{
    protected $fillable = [
        'type',
        'message',
        'file',
        'line',
        'context'
    ];

    protected $casts = [
        'context' => 'array'
    ];
}
