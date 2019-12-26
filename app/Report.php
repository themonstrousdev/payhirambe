<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends APIModel
{
  protected $table = 'reports';
  protected $fillable = ['account_id', 'request_id', 'message'];

  public function getAccountIdAttribute($value){
    return intval($value);
  }

  public function getRequestIdAttribute($value){
    return intval($value);
  }
}
