<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends APIModel
{
  protected $table = 'payments';
  protected $fillable = ['code', 'account_id', 'request_id', 'amount', 'date'];

  public function request(){
    return $this->belongsTo('App\RequestMoney');
  }
}
