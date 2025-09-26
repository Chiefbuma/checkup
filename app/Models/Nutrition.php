<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nutrition extends Model
{
    use HasFactory;

    protected $table = 'nutritions';

    protected $fillable = [
        'registration_id',
        'height',
        'weight',
        'bmi',
        'lower_limit_weight',
        'weight_limit_weight',
        'visceral_fat',
        'body_fat_percent',
        'notes_nutritionist',
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
        static::creating(function ($nutrition) {
            $nutrition->user_id = auth()->id();

            // Auto-calculation
            if ($nutrition->height && $nutrition->weight) {
                $nutrition->bmi = ($nutrition->weight / (($nutrition->height / 100) ** 2));
                $nutrition->lower_limit_weight = 18 * (($nutrition->height / 100) ** 2);
                $nutrition->weight_limit_weight = 25 * (($nutrition->height / 100) ** 2);
            }
        });

        static::updating(function ($nutrition) {
            if ($nutrition->height && $nutrition->weight) {
                $nutrition->bmi = ($nutrition->weight / (($nutrition->height / 100) ** 2));
                $nutrition->lower_limit_weight = 18 * (($nutrition->height / 100) ** 2);
                $nutrition->weight_limit_weight = 25 * (($nutrition->height / 100) ** 2);
            }
        });

        static::created(function ($nutrition) {
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'Created Nutrition',
                'details' => "Nutrition ID {$nutrition->id} for Registration {$nutrition->registration_id}"
            ]);
        });
    }
}
