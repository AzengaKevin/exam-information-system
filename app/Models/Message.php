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
        'exam_id',
        'type',
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

    /**
     * Message sender (User) relations
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Message recipient (User) relations
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    /**
     * exam_id field mutator and changing of the type of message
     * 
     * @param mixed $value
     */
    public function setExamId($value)
    {
        if (!is_null($value)) {
            $this->attributes['exam_id'] = $value;
            $this->attributes['type'] = 'exam-result';
        }

    }

    /**
     * Get all possible message types
     * 
     * @return array
     */
    public static function typeOptions() : array
    {
        return array(
            'direct',
            'exam-result',
            'auth',
            'bulk',
            'other'
        );
    }
}
