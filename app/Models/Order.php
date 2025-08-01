<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'status_id',
        'completed_at',
    ];

    protected $casts = [
        'deadline_at' => 'datetime',
    ];

    public function getCreatedDateAttribute()
    {
        return $this->updated_at->format('d/m/Y H:i');
    }

    public function getDeadlineDateAttribute()
    {
        return $this->deadline_at->format('d/m/Y H:i');
    }

    public function expert(): BelongsTo
    {
        return $this->belongsTo(User::class, 'expert_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function typeWork(): BelongsTo
    {
        return $this->belongsTo(TypeWork::class);
    }

    public function plagiarismPlatform(): BelongsTo
    {
        return $this->belongsTo(PlagiarismPlatform::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }
}
