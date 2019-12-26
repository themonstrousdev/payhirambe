<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestPeer extends APIModel
{
  protected $table = 'request_peers';
  protected $fillable = ['request_id', 'account_id', 'charge', 'currency', 'status'];

  public function getAccountIdAttribute($value){
    return intval($value);
  }

  public function getRequestIdAttribute($value){
    return intval($value);
  }

  public function getChargeAttribute($value){
    return doubleval($value);
  }
}
