<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'phone',
        'wats_app',
        'telegram',
        'password',
        'role_id',
        'is_approved',
        'tg_id',
        'avatar',
        'description',
        'hidden_field',
        'last_active_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function adminlte_profile_url(): string
    {
        return '/admin/profile';
    }

    public function adminlte_desc(): string
    {
        return auth()->user()->role->title;
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function orders(): hasMany
    {
        return $this->hasMany(Order::class, 'expert_id', 'id');
    }

    public function getUpdatedDateAttribute()
    {
        return $this->updated_at->format('d/m/Y H:i');
    }

    public function getCreatedDateAttribute()
    {
        return $this->updated_at->format('d/m/Y H:i');
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class);
    }

    public function typeWorks(): BelongsToMany
    {
        return $this->belongsToMany(TypeWork::class);
    }

    public function isOnline(): bool
    {
        return Carbon::parse($this->last_active_at ?? '2025-01-01')
            ->gt(now()->subMinutes(2));
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'receiver_id', 'id');
    }

}
