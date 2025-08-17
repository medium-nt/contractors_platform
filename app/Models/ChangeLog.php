<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChangeLog extends Model
{
    protected $table = 'change_logs';

    protected $fillable = [
        'order_id',
        'user_id',
        'message',
    ];
}
