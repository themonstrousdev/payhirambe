<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Investment extends APIModel
{
  protected $table = 'investments';
  protected $fillable = ['account_id', 'code', 'request_id', 'amount', 'status'];

  public function getRequestIdAttribute($value){
    return intval($value);
  }

  public function getAccountIdAttribute($value){
    return intval($value);
  }

  public function getAmountAttribute($value){
    return doubleval($value);
  }

  public function getStatusAttribute($value){
    return intval($value);
  }
}
