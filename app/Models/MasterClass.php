<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Carbon $date
 * @property string $start_time
 * @property int $max_participants
 * @property float $price
 */
class MasterClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_id',
        'type_id',
        'title',
        'description',
        'date',
        'start_time',
        'max_participants',
        'price',
    ];

    protected $casts = [
        'date' => 'date',
        'price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Связи
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function type()
    {
        return $this->belongsTo(CreativityType::class, 'type_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // Кастомный атрибут: количество свободных мест
    public function getFreeSeatsAttribute(): int
    {
        return max(0, $this->max_participants - $this->bookings()->count());
    }

    // Проверка, можно ли записаться
    public function canBook(User $user): bool
    {
        if ($this->free_seats <= 0) {
            return false;
        }

        // Проверяем, не записан ли пользователь уже
        return ! $this->bookings()->where('user_id', $user->id)->exists();
    }

    // Форматирование даты и времени для отображения
    public function getFormattedDateTimeAttribute(): string
    {
        return $this->date->format('d.m.Y').' '.$this->start_time;
    }

    // Продолжительность всегда 2 часа (по ТЗ)
    public function getEndTimeAttribute(): string
    {
        $startTime = Carbon::parse($this->start_time);

        return $startTime->addHours(2)->format('H:i');
    }
}
