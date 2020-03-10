<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Coupon;
use Carbon\Carbon;
class CouponController extends APIController
{

  public $CouponAccountController = 'App\Http\Controllers\CouponAccountController';
  
  function __construct(){
    $this->localization();
    $this->model = new Coupon();
  }

  public function retrieveByValidation(Request $request){
    $data = $request->all();
    $this->model = new Coupon();
    $this->retrieveDB($data);
    $result = $this->response['data'];
    if(sizeof($result) > 0){
      $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $result[0]['start']);
      $expiryDate = Carbon::createFromFormat('Y-m-d H:i:s', $result[0]['end']);
          
      $currentDate = Carbon::now();
      $diffStart = $currentDate->diffInSeconds($startDate, false);
      $diffEnd = $currentDate->diffInSeconds($expiryDate, false);
      if($diffStart < 0 && $diffEnd >= 0){
        $id = $result[0]['id'];
        $accountId = $data['account_id'];
        $size = app($this->CouponAccountController)->getTotalSize($id);
        $limit = intval($result[0]['limit']);
        if(app($this->CouponAccountController)->checkIfExist($id, $accountId) > 0){
          $this->response['data'] = null;
          $this->response['error'] = 'You already used the coupon!';
        }else if($limit <= $size){
          $this->response['data'] = null;
          $this->response['error'] = 'Coupon was already used!';
        }else{
          $this->response['data'] = $result[0];
          $this->response['error'] = null;
        }
      }else{
        $this->response['data'] = null;
        $this->response['error'] = 'Expired Coupon Code';
      }
    }else{
      $this->response['data'] = null;
      $this->response['error'] = 'Invalid Coupon Code';
    }
    return $this->response();
  }
}
