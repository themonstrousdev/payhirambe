<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends APIModel
{
  protected $table = 'payments';
  protected $fillable = ['code', 'account_id', 'request_id', 'amount', 'date'];

  public function getAccountIdAttribute($value){
    return intval($value);
  }

  public function getRequestIdAttribute($value){
    return intval($value);
  }

  public function getAmountAttribute($value){
    return doubleval($value);
  }
}
