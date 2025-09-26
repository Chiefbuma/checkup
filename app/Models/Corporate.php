<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Corporate extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }
}
