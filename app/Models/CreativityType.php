<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreativityType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function masterClasses()
    {
        return $this->hasMany(MasterClass::class);
    }
}
