<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Payment;
use Carbon\Carbon;
class PaymentController extends APIController
{

  public $requestClass = 'App\Http\Controllers\RequestMoneyController';
  
  function __construct(){
    $this->model = new Payment();
  }

  public function retrieve(Request $request){
    $data = $request->all();
    $result = Payment::where('account_id', '=', $data['account_id'])->where($data['column'], 'like', $data['value'])->limit(intval($data['limit']))->offset(intval($data['offset']))->orderBy($data['sort']['column'], $data['sort']['value'])->get();

    if(sizeof($result) > 0){
      $i = 0;
      foreach ($result as $key) {
        $result[$i]['request'] = app($this->requestClass)->retrieveById($result[$i]['request_id']);
        $result[$i]['date_human'] = Carbon::createFromFormat('Y-m-d', $result[$i]['date'])->copy()->tz('Asia/Manila')->format('F j, Y');
        $result[$i]['created_at_human'] = Carbon::createFromFormat('Y-m-d H:i:s', $result[$i]['created_at'])->copy()->tz('Asia/Manila')->format('F j, Y H:i A');
        $i++;
      }
    }

    return response()->json(array(
      'data'        => $result,
      'error'       => null,
      'billing'     => app($this->requestClass)->payments($data),
      'timestamps'  => Carbon::now()
    ));
  }

  public function create(Request $request){
    $data = $request->all();
    $data['code'] = $this->generateCode();
    $this->model = new Payment();
    $this->insertDB($data);
    return $this->response();
  }

  public function generateCode(){
    $code = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 32);
    $codeExist = Payment::where('code', '=', $code)->get();
    if(sizeof($codeExist) > 0){
      $this->generateCode();
    }else{
      return $code;
    }
  }
}
