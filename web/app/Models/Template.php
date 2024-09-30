<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    public function has_sender_profile(){
        return  $this->belongsTo('App\Models\SenderProfile', 'sender_profile_id', 'id');
    }
}
