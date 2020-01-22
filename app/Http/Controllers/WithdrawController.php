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
    $this->model = new Withdraw();
  }
  public function create(Request $request){
      $response = array(
        'data'  => null,
        'otp'   => null,
        'error' => null,
        'timestamps' => Carbon::now()
      );
      $data = $request->all();
      $amount = floatval($data['amount']);
      $myBalance = floatval(app($this->ledgerClass)->retrievePersonal($data['account_id']));
      $description = 'test';
      $accountId = $data['account_id'];
      if($myBalance < $amount){
        $response['error'] = 'You have insufficient balance. Your balance is PHP '.$myBalance.' balance.';
      }else{
        if($data['otp'] == 0){
          $code = app($this->notificationClass)->generateOTPFundTransfer($data['account_id']);
          $response['otp'] = true;
        }else if($data['otp'] == 1){
          $charge = floatval($data['charge']);
          $withdrawModel = new Withdraw();
          $withdrawModel->code = $this->generateCode();
          $withdrawModel->account_id = $data['account_id'];
          $withdrawModel->amount = $amount;
          $withdrawModel->charge = $charge;
          $withdrawModel->created_at = Carbon::now();
          $withdrawModel->payload = $data['payload'];
          $withdrawModel->payload_value = $data['payload_value'];
          $withdrawModel->otp_code = $data['otp_code'];
          $withdrawModel->save();
          $response['data'] = $withdrawModel->id;
        }
      }
    return response()->json($response);
  }

  public function retrieve(Request $request){
    $data = $request->all();
    $this->model = new Withdraw();
    $this->retrieveDB($data);
    $result = $this->response['data'];
    if(sizeof($result) > 0){
      $i = 0;
      foreach ($result as $key) {
        $this->response['data'][$i]['created_at_human'] = Carbon::createFromFormat('Y-m-d H:i:s', $result[$i]['created_at'])->copy()->tz($this->response['timezone'])->format('F j, Y h:i A');
        $i++;
      }
    }
    return $this->response();
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
}
