<?php

namespace App\Http\Controllers;
use App\CouponAccount;
use App\Coupon;
use Illuminate\Http\Request;

class CouponAccountController extends APIController
{
  function __construct(){
    $this->model = new CouponAccount();
  }

  public function getTotalSize($couponId){
    return CouponAccount::where('coupon_id', '=', $couponId)->count();
  }

  public function checkIfExist($couponId, $accountId){
    return CouponAccount::where('coupon_id', '=', $couponId)->where('account_id', '=', $accountId)->count();
  }

  public function insert($data){
    return CouponAccount::insert($data);
  }

  public function getByAccountIdAndPayload($accountId, $payload, $payloadValue){
    $result = CouponAccount::where('account_id', '=', $accountId)->where('payload', '=', $payload)->where('payload_value', '=', $payloadValue)->get();
    if(sizeof($result) > 0){
      $result = Coupon::where('id', '=', $result[0]['coupon_id'])->get();
      return sizeof($result) > 0 ? $result[0] : null;
    }else{
      return null;
    }
  }
}
