<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Land;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static create(array $array)
 */
class Farmer extends Model
{
    use HasFactory;
    protected $fillable=[
        'land_id','user_id'
        ];

    public function land(): BelongsTo
    {
        return $this->belongsTo(Land::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
