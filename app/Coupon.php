<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coupon extends APIModel
{
    protected $table = 'coupons';
    protected $fillable = ['id', 'account_id','code', 'currency', 'country', 'locality', 'type', 'amount', 'limit', 'start', 'end'];

	public function getAccountIdAttribute($value){
	return intval($value);
	}

	public function getAmountAttribute($value){
	return intval($value);
	}

	public function getLimitAttribute($value){
	return intval($value);
	}
}
