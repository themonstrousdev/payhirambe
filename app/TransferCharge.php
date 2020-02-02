<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransferCharge extends APIModel
{
  protected $table = 'transfer_charges';
  protected $fillable = ['currency', 'type', 'min_amount', 'max_amount', 'charge'];

  public function getMinAmountAttribute($value){
    return doubleval($value);
  }

  public function getMaxAmountAttribute($value){
    return doubleval($value);
  }

  public function getChargeAttribute($value){
    return doubleval($value);
  }
}
