<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    protected $casts = [
        'start_date' => 'datetime:d-m-Y',
        'end_date' => 'datetime:d-m-Y',
    ];
    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'priority',
        'is_done',
    ];
}
