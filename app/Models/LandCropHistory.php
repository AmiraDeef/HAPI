<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandCropHistory extends Model
{
    use HasFactory;
    protected $fillable=[
        'land_id',
        'crop_id',
        'start_date',
        'end_date',
    ];
    public function land()
    {
        return $this->belongsTo(Land::class);
    }

    public function crop()
    {
        return $this->belongsTo(Crop::class);
    }
}
