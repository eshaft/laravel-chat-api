<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Chat extends Eloquent
{
    public const UPDATED_AT = 'updatedAt';
    public const CREATED_AT = 'createdAt';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'creator', 'title', 'members',
    ];

    public function chatCreator()
    {
        return $this->belongsTo('App\User', 'creator', '_id');
    }

    public function scopeMy($query)
    {
        return $query->where('creator', '=', auth()->user()->_id);
    }
}
