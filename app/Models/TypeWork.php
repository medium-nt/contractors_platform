<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeWork extends Model
{
    protected $table = 'types_work';

    protected $fillable = [
        'title',
        'is_active'
    ];
}
