<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'code',
        'questions',
        'status',
    ];

    protected $casts = [
        'questions' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attempts(){
        return $this->hasMany(QuizAttempt::class);
    }
}