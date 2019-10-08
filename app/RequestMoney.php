<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestMoney extends APIModel
{
    protected $table = 'requests';
    protected $fillable = ['account_id', 'code', 'amount', 'months_payable', 'interest', 'reason', 'needed_on', 'billing_per_month', 'status', 'approved_status'];

    public function payments(){
      return $this->hasMany('App\Payments');
    }
}
