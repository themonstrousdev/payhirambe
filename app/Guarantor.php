<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Guarantor extends APIModel
{
    protected $table = 'guarantors';
    protected $fillable = ['code', 'sender_id', 'reciever_id','email','status'];
}
