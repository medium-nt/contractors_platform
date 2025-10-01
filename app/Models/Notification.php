<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    //
    protected $fillable = [
        'read_at',
        'type'
    ];

    // Типы уведомлений
    public const TYPE_CALENDAR = 'calendar';
    public const TYPE_FILE_UPLOAD = 'file_upload';
    public const TYPE_CHAT = 'chat';
    public const TYPE_STATUS = 'status';

    public static function getTypeLabels(): array
    {
        return [
            self::TYPE_FILE_UPLOAD => 'Файлы',
            self::TYPE_CHAT => 'Чаты',
            self::TYPE_CALENDAR => 'Календарь',
            self::TYPE_STATUS => 'Статусы',
        ];
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
