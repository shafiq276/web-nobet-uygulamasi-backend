<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable ,HasUuids;

    
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;


    protected $fillable = [
        'firstname',
        'lastname',
        'username',
        'phoneNumber',
        'email',
        'gender',
        'password',
        'role',
        'profilePhotoUrl',
        'adminID'
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * JWT için gerekli metodlar
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role,
            'email' => $this->email,
            'username' => $this->username
        ];
    }

    // İlişkiler
    public function offdays()
    {
        return $this->hasMany(Offday::class, 'userId');
    }

    public function shiftdays()
    {
        return $this->hasMany(Shiftday::class, 'userId');
    }

    public function specialtasks()
    {
        return $this->hasMany(Specialtask::class, 'userId');
    }
}