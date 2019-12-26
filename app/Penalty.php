<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Penalty extends APIModel
{
  protected $table = 'penalties';
  protected $fillable = ['code', 'account_id', 'request_id', 'amount', 'date', 'status'];

  public function getAccountIdAttribute($value){
    return intval($value);
  }

  public function getRequestIdAttribute($value){
    return intval($value);
  }

  public function getAmountAttribute($value){
    return doubleval($value);
  }

  public function getStatusAttribute($value){
    return intval($value);
  }
}
