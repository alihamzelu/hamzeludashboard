<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskMessageAttachment extends Model
{
    protected $fillable = [
        'task_message_id',
        'path',
        'original_name',
        'mime_type',
        'size',
    ];

    public function taskMessage(): BelongsTo
    {
        return $this->belongsTo(TaskMessage::class);
    }
}
