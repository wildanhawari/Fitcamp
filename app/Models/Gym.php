<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Gym extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'thumbnail',
        'about',
        'city_id',
        'open_time_at',
        'closed_time_at',
        'is_popular',
        'address',
    ];

    public function setNameAttribute($value) {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function city(): BelongsTo {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function gymTestimonials(): HasMany {
        return $this->hasMany(GymTestimonial::class);
    }

    public function gymPhotos(): HasMany {
        return $this->hasMany(GymPhoto::class);
    }

    public function gymFacilities(): HasMany {
        return $this->hasMany(GymFacility::class);
    }
}
