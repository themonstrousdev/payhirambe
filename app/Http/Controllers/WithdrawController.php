<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Withdraw;
use Mail;
use App\Mail\OtpEmail;
use Carbon\Carbon;
use App\NotificationSetting;

class WithdrawController extends APIController
{
  public $ledgerClass = 'App\Http\Controllers\LedgerController';
  public $notificationClass = 'App\Http\Controllers\NotificationSettingController';
  
  function __construct(){
    $this->localization();
    $this->model = new Withdraw();
  }

  public function create(Request $request){
      $data = $request->all();
      $amount = floatval($data['amount']) + floatval($data['charge']);
      $myBalance = floatval(app($this->ledgerClass)->retrievePersonal($data['account_id']));
      $description = 'test';
      $accountId = $data['account_id'];
      if($myBalance < $amount){
        $this->response['error'] = 'You have insufficient balance. Your current balance is '.$data['currency'].' '.$myBalance.' balance.';
      }else{
        $this->model = new Withdraw();
        $data['status'] = 'pending';
        $data['code'] = $this->generateCode();
        $this->insertDB($data);
      }
    return $this->response();
  }

  public function retrieve(Request $request){
    $data = $request->all();
    $this->model = new Withdraw();
    $this->retrieveDB($data);
    $result = $this->response['data'];
    if(sizeof($result) > 0){
      $i = 0;
      foreach ($result as $key) {
        $this->response['data'][$i]['account'] = $this->retrieveAccountDetails($result[$i]['account_id']);
        $this->response['data'][$i]['created_at_human'] = Carbon::createFromFormat('Y-m-d H:i:s', $result[$i]['created_at'])->copy()->tz($this->response['timezone'])->format('F j, Y h:i A');
        $i++;
      }
    }
    $this->response['ledger'] = app($this->ledgerClass)->retrievePersonal($data['account_id']);
    return $this->response();
  }

  public function getTotalSumByParams($column, $value){
    $result = Withdraw::where('status', '=', 'pending')->where($column, '=', $value)->get();
    $total = 0;
    if(sizeof($result) > 0){
      $i = 0;
      foreach ($result as $key) {
        $total += doubleval($result[$i]['amount']) + doubleval($result[$i]['charge']);
        $i++;
      }
    }
    return doubleval($total);
  }

    public function generateCode(){
    $code = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 32);
    $codeExist = Withdraw::where('code', '=', $code)->get();
    if(sizeof($codeExist) > 0){
      $this->generateCode();
    }else{
      return $code;
    }
  }

  public function updateByParams($column, $value){
    Withdraw::where($column, '=', $value)->update(array(
      'status' => 'completed',
      'updated_at' => Carbon::now()
    ));
    return true;
  }
}
