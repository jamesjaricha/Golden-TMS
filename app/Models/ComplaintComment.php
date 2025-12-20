<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplaintComment extends Model
{
    protected $fillable = [
        'complaint_id',
        'user_id',
        'comment',
        'is_internal',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
    ];

    /**
     * Get the complaint that owns the comment.
     */
    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    /**
     * Get the user that wrote the comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
