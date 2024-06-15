<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CropLandHistory extends Model
{
    use HasFactory;
    protected $fillable=[
        'land_id',
        'crop_id',
        'planted_at',
        'harvested_at',
        'nitrogen_applied', 'phosphorus_applied', 'potassium_applied'
    ];
    public $timestamps = false;
    public function land()
    {
        return $this->belongsTo(Land::class);
    }

    public function crop()
    {
        return $this->belongsTo(Crop::class);
    }
}
