<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @method static create(array $user)
 * @property mixed $id
 * @property mixed $landowner
 * @property mixed $farmer
 * @property mixed $role
 * @property mixed $land
 * @property mixed $username
 *
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'phone_number',
        'password',
        'role',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function landowner(): HasOne
    {
        return $this->hasOne(Landowner::class);
    }

    public function farmer(): HasOne
    {
        return $this->hasOne(Farmer::class);
    }
    public function notification()
    {
        return $this->hasMany(Notification::class);
    }
    public function land(): BelongsTo
    {
        return $this->belongsTo(Land::class);
    }


}
