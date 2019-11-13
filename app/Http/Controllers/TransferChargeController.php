<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TransferCharge;
use Carbon\Carbon;
class TransferChargeController extends APIController
{
  function __construct(){
    $this->model = new TransferCharge();
  }


  public function retrieve(Request $request){
    $data = $request->all();
    $this->model = new TransferCharge();
    $this->retrieveDB($data);
    $result = $this->response['data'];
    if(sizeof($result) > 0){
      $i = 0;
      foreach ($result as $key) {
        $this->response['data'][$i]['created_at_human'] = Carbon::createFromFormat('Y-m-d H:i:s', $result[$i]['created_at'])->copy()->tz('Asia/Manila')->format('F j, Y H:i A');
        $i++;
      }
    }
    return $this->response();
  }
}
