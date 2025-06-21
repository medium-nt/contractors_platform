<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'title',
        'description',
        'type_work_id',
        'subject_id',
        'text_uniqueness',
        'plagiarism_platform_id',
        'price',
        'hidden_field',
        'deadline_at',
        'warranty_up_to',
        'expert_id',
        'manager_id',
    ];

    public function getCreatedDateAttribute()
    {
        return $this->updated_at->format('d/m/Y H:i');
    }

}
