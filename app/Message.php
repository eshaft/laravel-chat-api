<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Message extends Eloquent
{
    public const UPDATED_AT = 'updatedAt';
    public const CREATED_AT = 'createdAt';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'content', 'chatId', 'sender', 'statusMessage'
    ];

    public function messageSender()
    {
        return $this->belongsTo('App\User', 'sender', '_id');
    }
}
