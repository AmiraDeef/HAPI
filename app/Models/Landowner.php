<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @method static create(array $array)
 */
class Landowner extends Model
{
    use HasFactory;
    protected $fillable=[
        'user_id',
        //'unique_land_id',
        'land_id',
    ];

    public function farmers(): HasMany
    {
        return $this->hasMany(Farmer::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function land(): HasOne
    {
        return $this->hasOne(Land::class);
    }
}
