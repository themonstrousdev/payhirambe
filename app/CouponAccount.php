<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CouponAccount extends APIModel
{
    protected $table = 'coupon_accounts';
    protected $fillable = ['id', 'account_id','coupon_id', 'payload', 'payload_value'];

	public function getAccountIdAttribute($value){
	return intval($value);
	}

	public function getCouponIdAttribute($value){
	return intval($value);
	}

	public function getPayloadValueAttribute($value){
	return intval($value);
	}
}
