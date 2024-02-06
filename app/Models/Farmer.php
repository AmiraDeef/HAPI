<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Farm;

/**
 * @method static create(array $array)
 */
class Farmer extends Model
{
    use HasFactory;
    protected $fillable=[
        'farm_id','user_id'
        ];

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
