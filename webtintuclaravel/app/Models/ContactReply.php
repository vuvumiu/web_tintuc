<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactReply extends Model
{
    protected $table = 'contact_replies';
    protected $primaryKey = 'id';

    protected $fillable = [
        'contact_id',
        'staff_id',
        'staff_name',
        'reply_intro',
        'reply_content',
        'reply_outro',
        'recipient_email',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'RowID');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
