<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Detection extends Model
{
    use HasFactory;
    protected $fillable=[
        'user_id','farm_id','image','detection','detected_at'
    ];
    public $timestamps = false;

  public function user(): BelongsTo
  {
        return $this->belongsTo(User::class);
    }


    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }


}
