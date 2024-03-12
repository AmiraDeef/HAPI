<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Land extends Model
{
    use HasFactory;
    protected $fillable=[
        'unique_land_id',
        'crop_id',
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
    public function crops()
    {
        return $this->belongsTo(Crop::class);
    }

    public function cropHistory()
    {
        return $this->hasMany(CropLandHistory::class);
    }
    public function iot()
    {
        return $this->hasOne(Iot::class);
    }
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
}
