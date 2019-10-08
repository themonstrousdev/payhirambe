<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pulling;
use Carbon\Carbon;

class PullingController extends APIController
{
   function __construct(){
      $this->model = new Pulling();
    }
    public function retrieve(Request $request){
      $data = $request->all();
      $result = Pulling::where('request_id', '=', $data['requestId'])->get();
      return response()->json(array(
        'data' => sizeof($result) > 0 ? $result : null
      ));
    }
    public function getTotalByParams($column, $value){
      $result = Pulling::where($column, '=', $value)->sum('amount');
      return $result;
    }
    
    public function addToPulling($accountId, $amount, $requestId){
      $pulling = new Pulling();
      $pulling->account_id = $accountId;
      $pulling->request_id = $requestId;
      $pulling->amount = $amount;
      $pulling->created_at = Carbon::now();
      $pulling->save();
    }
}
