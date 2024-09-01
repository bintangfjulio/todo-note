<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'scheduled_at',
        'is_done',
        'completed_at',
        'order',
        'attachment',
        'attachment_name',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $maxOrder = static::max('order');
            $model->order = $maxOrder ? $maxOrder + 1 : 1;
        });
    }
}
