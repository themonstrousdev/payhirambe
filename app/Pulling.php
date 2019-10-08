<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pulling extends APIModel
{
    protected $table = 'pullings';
    protected $fillable = ['request_id', 'account_id', 'amount'];
}

