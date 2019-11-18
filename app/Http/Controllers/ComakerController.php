<?php

namespace App\Http\Controllers;

use Increment\Account\Models\Account;
use Illuminate\Http\Request;
use App\Comaker;
use Carbon\Carbon;
class ComakerController extends APIController
{

    public $ratingClass = 'Increment\Common\Rating\Http\RatingController';
    public $workClass = 'App\Http\Controllers\WorkController';
    public $cardClass = 'App\Http\Controllers\AccountCardController';
    public $educationClass = 'App\Http\Controllers\EducationController';
    public $guarantorClass = 'App\Http\Controllers\GuarantorController';

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
      $result = Comaker::where('account_id', '=', $accountId)->where('request_id', '=', $requestId)->get();
      if(sizeof($result) > 0){
        $i = 0;
        foreach ($result as $key) {
          $result[$i]['account'] = $this->retrieveAccountDetails($result[$i]['comaker']);
          $result[$i]['rating'] = app($this->ratingClass)->getRatingByPayload('profile', $result[$i]['comaker']);
          $result[$i]['works'] = app($this->workClass)->getByParams('account_id', $result[$i]['comaker']);
          $result[$i]['cards'] = app($this->cardClass)->getByParams('account_id', $result[$i]['comaker']);
          $result[$i]['guarantors'] = app($this->guarantorClass)->getByParams('sender', $result[$i]['comaker']);
          $result[$i]['educations'] = app($this->educationClass)->getByParams('account_id', $result[$i]['comaker']);
          $i++;
        }
      }
      return sizeof($result) > 0 ? $result : null;
    }

}
