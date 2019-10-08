<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Withdraw extends APIModel
{
    protected $table = 'withdraws';
    protected $fillable = ['code', 'account_id', 'amount', 'status', 'otp_code'];
}

