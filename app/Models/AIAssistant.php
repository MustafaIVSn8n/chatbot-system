<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIAssistant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'assistant_id',
        'name',
        'model',
        'instructions',
        'description',
        'is_active'
    ];

    /**
     * The attributes that should be cast to nullable types.
     *
     * @var array
     */
    protected $nullable = [
        'name',
        'model',
        'instructions',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'name' => 'nullable',
        'model' => 'nullable',
        'instructions' => 'nullable',
        'description' => 'nullable',
    ];
}
