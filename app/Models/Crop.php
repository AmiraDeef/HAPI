<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crop extends Model
{
    use HasFactory;
    protected $fillable=[
        'crop_name',

        'crop_description',

    ];
    public function detection()
    {
        return $this->hasMany(Detection::class);
    }
    public function land()
    {
        return $this->belongsTo(Land::class);
    }
    public function landHistory()
    {
        return $this->hasMany(LandCropHistory::class);
    }
}
