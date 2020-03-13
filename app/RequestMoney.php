<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestMoney extends APIModel
{
  protected $table = 'requests';
  protected $fillable = ['account_id', 'code', 'type', 'amount', 'months_payable', 'interest', 'reason', 'needed_on', 'billing_per_month', 'status', 'approved_status', 'max_charge'];

  public function getAccountIdAttribute($value){
    return intval($value);
  }

  public function getTypeAttribute($value){
    return intval($value);
  }

  public function getAmountAttribute($value){
    return doubleval($value);
  }

  public function getMonthsPayableAttribute($value){
    return intval($value);
  }

  public function getInterestAttribute($value){
    return intval($value);
  }

  public function getBillingPerMonthAttribute($value){
    return intval($value);
  }

  public function getStatusAttribute($value){
    return intval($value);
  }
}
