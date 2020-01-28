<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Deposit;
use App\DepositAttachment;
use Carbon\Carbon;

class DepositController extends APIController
{
    public $requestClass = 'App\Http\Controllers\RequestMoneyController';
    public $ledgerClass = 'App\Http\Controllers\LedgerController';
    public $emailClass = 'App\Http\Controllers\EmailController';
    
    function __construct(){
      $this->model = new Deposit();

      $this->notRequired =  array(
        'description',
        'deposit_slip'
      );
    }

    public function retrieve(Request $request){
      $data = $request->all();
      $this->retrieveDB($data);
      $result = $this->response['data'];
      if(sizeof($result) > 0){
        $i = 0;
        foreach ($result as $key) {
          $attachments = DepositAttachment::where('deposit_id','=',$result[$i]['id'])->get();
          $this->response["data"][$i]['created_at_human'] = Carbon::createFromFormat('Y-m-d H:i:s', $result[$i]['created_at'])->copy()->tz($this->response['timezone'])->format('F j, Y H:i A');
          $this->response["data"][$i]['attachments'] = $attachments;
          $this->response['data'][$i]['account'] = $this->retrieveAccountDetails($result[$i]['account_id']);
          $i++;
        }
      }

      return $this->response();
    }

    public function create(Request $request){
      $data = $request->all();
      $data['code'] = $this->generateCode();
      $this->model = new Deposit();
      $this->insertDB($data);
      if($this->response['data'] > 0){
        $deposit = Deposit::where('id', '=', $this->response['data'])->get();
        $result = $deposit[0];
        app($this->emailClass)->deposit($data['account_id'], $result, 'Payment Confirmation:'.$result['code']);
      }
      return $this->response();
    }

    public function generateCode(){
      $code = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 32);
      $codeExist = Deposit::where('code', '=', $code)->get();
      if(sizeof($codeExist) > 0){
        $this->generateCode();
      }else{
        return $code;
      }
    }
}
