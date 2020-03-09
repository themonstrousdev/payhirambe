<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountCoupon extends APIModel
{
    protected $table = 'account_coupons';
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
