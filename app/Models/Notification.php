<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static findOrFail($id)
 * @method static create(array $array)
 * @method static where(string $string, $land_id)
 * @method static whereIn(string $string, string[] $array)
 *
 */
class Notification extends Model
{
    use HasFactory;
    protected $fillable=[
       'land_id',
        'user_id',
        'message',
        'status',
        'type',
        'created_at',
        'updated_at',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function land()
    {
        return $this->belongsTo(Land::class);
    }

}
