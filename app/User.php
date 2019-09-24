<?php

namespace App;

use \Jenssegers\Mongodb\Auth\User as EloquentAuthUser;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;

class User extends EloquentAuthUser implements JWTSubject
{
    use Notifiable;

    public const UPDATED_AT = 'updatedAt';
    public const CREATED_AT = 'createdAt';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'firstName', 'lastName', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function userChats()
    {
        return $this->hasMany('App\Chat', 'creator', '_id');
    }

    public function userMessages()
    {
        return $this->hasMany('App\Message', 'sender', '_id')->where('statusMessage', '=', false);
    }
}
