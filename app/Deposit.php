<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Deposit extends APIModel
{
    protected $table = 'deposits';
    protected $fillable = ['code', 'account_id', 'amount', 'description'];
}

