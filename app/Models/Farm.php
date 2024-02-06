<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farm extends Model
{
    use HasFactory;
    protected $fillable=[
        'unique_farm_id'
    ];
    public function landowner()
    {
        return $this->belongsTo(Landowner::class);
    }

    public function farmers() {
        return $this->hasMany(Farmer::class);
    }
}
