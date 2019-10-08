<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Penalty extends APIModel
{
    protected $table = 'penalties';
    protected $fillable = ['code', 'account_id', 'request_id', 'amount', 'date', 'status'];

    public function requests(){
      return $this->hasOne('App\RequestMoney');
    }
}
