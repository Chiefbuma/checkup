<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;   // ✅ add this

class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'middle_name',
        'surname',
        'sex',
        'dob',
        'age',
        'phone',
        'corporate_id',
        'email',
        'user_id',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    public function vitals()
    {
        return $this->hasMany(Vital::class);
    }

    public function nutritions()
    {
        return $this->hasMany(Nutrition::class);
    }

    public function clinicals()
    {
        return $this->hasMany(Clinical::class);
    }
    public function corporate()
{
    return $this->belongsTo(Corporate::class, 'corporate_id');
}


    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
     protected static function booted()
    {
        static::creating(fn($model) => $model->user_id = auth()->id());
        static::created(fn($model) => ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Created Registration',
            'details' => "Registration ID {$model->id}"
        ]));
    }
     // 🔹 Ensure age is always updated before saving
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->dob) {
                $model->age = Carbon::parse($model->dob)->age;
            }
        });
    }

}
