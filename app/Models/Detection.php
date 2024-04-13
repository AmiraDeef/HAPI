<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Detection extends Model
{
    use HasFactory;
    protected $fillable=[
        'user_id','land_id','crop','image','detection','detected_at'
    ];
    public $timestamps = false;

  public function user(): BelongsTo
  {
        return $this->belongsTo(User::class);
    }


    public function land(): BelongsTo
    {
        return $this->belongsTo(Land::class);
    }


}
