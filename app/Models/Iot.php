<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Iot extends Model
{
    use HasFactory;
    protected $table = 'iot_data';
    protected $fillable=[
        'land_id','data','created_at','updated_at'
    ];
    public function land(){
        return $this->belongsTo(Land::class);
    }

}
