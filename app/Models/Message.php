<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sender_id',
        'recipient_id',
        'content'
    ];

    /**
     * It is a scope method for gettings all the messages for a certain user, both sent and received
     */
    public function scopeFor($query, User $user)
    {
        $query->where('sender_id', $user->id)
            ->orWhere('recipient_id', $user->id);
    }
}
