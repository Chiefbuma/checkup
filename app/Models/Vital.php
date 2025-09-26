<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vital extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_id',
        'bp_systolic',
        'bp_diastolic',
        'pulse',
        'temp',
        'rbs',
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
            'action' => 'Created Vital',
            'details' => "Vital ID {$m->id} for Registration {$m->registration_id}"
        ]));
    }
}
