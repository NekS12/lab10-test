<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'full_name',
        'email',
        'password',
        'phone',
        'role', // 'visitor' или 'instructor'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Связь: мастер-классы, созданные этим пользователем (если он ведущий)
    public function myClasses()
    {
        return $this->hasMany(MasterClass::class, 'instructor_id');
    }

    // Связь: записи этого пользователя на мастер-классы
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // Проверка роли
    public function isInstructor(): bool
    {
        return $this->role === 'instructor';
    }

    public function isVisitor(): bool
    {
        return $this->role === 'visitor';
    }
}