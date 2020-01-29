<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Withdraw extends APIModel
{
    protected $table = 'withdraws';
    protected $fillable = ['code', 'currency', 'charge', 'account_id', 'amount', 'bank', 'account_name', 'account_number', 'status'];

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

