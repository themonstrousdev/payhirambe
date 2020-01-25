<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvestorLocation extends APIModel
{
  protected $table = 'investor_locations';
  protected $fillable = ['account_id', 'country', 'locality'];

  public function getAccountIdAttribute($value){
    return intval($value);
  }
}