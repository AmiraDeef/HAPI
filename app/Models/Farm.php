<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Farm extends Model
{
    use HasFactory;
    protected $fillable=[
        'unique_farm_id'
    ];
    public function landowner(): BelongsTo
    {
        return $this->belongsTo(Landowner::class);
    }

    public function farmers(): HasMany
    {
        return $this->hasMany(Farmer::class);
    }
    public function detections(): HasMany
    {
        return $this->hasMany(Detection::class);
    }
}
