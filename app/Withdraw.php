<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Withdraw extends APIModel
{
    protected $table = 'withdraws';
    protected $fillable = ['code', 'account_id', 'amount', 'status', 'payload', 'payload_value', 'otp_code', 'status'];

    public function getAccountIdAttribute($value){
      return intval($value);
    }

    public function getAmountAttribute($value){
      return doubleval($value);
    }

    public function getChargeAttribute($value){
      return doubleval($value);
    }

}

