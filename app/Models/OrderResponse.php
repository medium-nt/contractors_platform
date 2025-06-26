<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderResponse extends Model
{
    protected $table = 'order_responses';

    protected $fillable = [
        'order_id',
        'expert_id',
        'comment',
    ];

    public function expert(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
