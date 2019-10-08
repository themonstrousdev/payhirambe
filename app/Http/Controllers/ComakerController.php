<?php

namespace App\Http\Controllers;

use Increment\Account\Models\Account;
use Illuminate\Http\Request;
use App\Comaker;
use Carbon\Carbon;
class ComakerController extends APIController
{
    public function addToComaker($accountId, $requestId, $comaker){
      $newComaker = new Comaker();
      $newComaker->account_id = $accountId;
      $newComaker->request_id = $requestId;
      $newComaker->comaker = $comaker;
      $newComaker->status = 0;
      $newComaker->created_at = Carbon::now();
      $newComaker->save();
    }


    public function getByParams($accountId, $requestId){
      $result = Comaker::where('account_id', '=', $accountId)->where('request_id', '=', $requestId)->where('status', '=', 'approved')->get();
      if(sizeof($result) > 0){
        $i = 0;
        foreach ($result as $key) {
          $result[$i]['account'] = $this->retrieveAccountDetails($result[$i]['comaker']);
          $i++;
        }
      }
      return sizeof($result) > 0 ? $result : null;
    }

}
