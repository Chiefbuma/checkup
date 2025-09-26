<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clinical extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_id',
        'notes_psychologist',
        'notes_doctor',
        'user_id',
    ];

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
    protected static function booted()
    {
        static::creating(fn($m) => $m->user_id = auth()->id());
        static::created(fn($m) => ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Created Clinical',
            'details' => "Clinical ID {$m->id} for Registration {$m->registration_id}"
        ]));
    }
}
