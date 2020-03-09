<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Coupon;
use Carbon\Carbon;

class CouponController extends APIController
{
    function __construct(){
      if($this->checkAuthenticatedUser() == false){
        return $this->response();
      }
      $this->model = new Coupon();
      $this->localization();

    public function create(Request $request){
      $data = $request->all();
      $data['code'] = $this->generateCode();
      $this->model = new Coupon();
      $this->insertDB($data);
      if($this->response['data'] > 0){
        $deposit = Deposit::where('id', '=', $this->response['data'])->get();
        $result = $deposit[0];
        app($this->emailClass)->deposit($data['account_id'], $result, 'Payment Confirmation:'.$result['code']);
      }
      return $this->response();
    }  
}
