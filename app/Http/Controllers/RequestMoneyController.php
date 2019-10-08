<?php

namespace App\Http\Controllers;
use Increment\Account\Models\Account;
use Illuminate\Http\Request;
use App\RequestMoney;
use Carbon\Carbon;
class RequestMoneyController extends APIController
{

		public $ratingClass = 'Increment\Common\Rating\Http\RatingController';
    public $comakerClass = 'App\Http\Controllers\ComakerController'; 
    public $investmentClass = 'App\Http\Controllers\InvestmentController';
    public $notificationClass = 'Increment\Common\Notification\Http\NotificationController';
    public $penaltyClass = 'App\Http\Controllers\PenaltyController';
    public $pullingClass = 'App\Http\Controllers\PullingController';
    public $workClass = 'App\Http\Controllers\WorkController';
    public $cardClass = 'App\Http\Controllers\AccountCardController';
    public $educationClass = 'App\Http\Controllers\EducationController';
    public $guarantorClass = 'App\Http\Controllers\GuarantorController';
    function __construct(){  
    	$this->model = new RequestMoney();

      $this->notRequired = array(
        'approved_date'
      );
    }

    public function create(Request $request){
    	$data = $request->all();
    	$data['code'] = $this->generateCode();
      $data['status'] = 0;
    	$this->model = new RequestMoney();
    	$this->insertDB($data);
      $getID = RequestMoney::where('code', '=', $data['code'])->get();
      $userExist = Account::where('email', '=', $data['comaker'])->get();
      if(sizeof($userExist) > 0){
        $comaker = $userExist[0]->id;
        app($this->comakerClass)->addToComaker($data['account_id'], $getID[0]->id, $comaker); 
        $parameter = array(
          'to' => $comaker,
          'from' => $data['account_id'],
          'payload' => 'comaker',
          'payload_value' => $getID[0]->id,
          'route' => '/requests/' + $data['request_id']
        );
      app($this->notificationClass)->create($parameter);
      }
    	return $this->response();
    }

    public function generateCode(){
      $code = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 32);
      $codeExist = RequestMoney::where('code', '=', $code)->get();
      if(sizeof($codeExist) > 0){
        $this->generateCode();
      }else{
        return $code;
      }
    }

    public function retrieve(Request $request){
    	$data = $request->all();
      $result = RequestMoney::where('status', '=', 0)->limit(intval($data['limit']))->offset(intval($data['offset']))->orderBy($data['sort']['column'], $data['sort']['value'])->get();
      $size =  RequestMoney::where('status', '=', 0)->get();
      if(sizeof($result) > 0){
        $i = 0;
        foreach ($result as $key) {
          $invested = app($this->investmentClass)->invested($result[$i]['id']);
          $amount = floatval($result[$i]['amount']);
          $result[$i]['rating'] = app($this->ratingClass)->getRatingByPayload('profile', $result[$i]['account_id']);
          $result[$i]['pulling'] = app($this->pullingClass)->getTotalByParams('request_id', $result[$i]['id']);
          $result[$i]['account'] = $this->retrieveAccountDetails($result[$i]['account_id']);
          $result[$i]['works'] = app($this->workClass)->getByParams('account_id', $result[$i]['account_id']);
          $result[$i]['cards'] = app($this->cardClass)->getByParams('account_id', $result[$i]['account_id']);
          $result[$i]['guarantors'] = app($this->guarantorClass)->getByParams('sender', $result[$i]['account_id']);
          $result[$i]['educations'] = app($this->educationClass)->getByParams('account_id', $result[$i]['account_id']);
          $result[$i]['comakers'] = app($this->comakerClass)->getByParams($result[$i]['account_id'], $result[$i]['id']);
          $result[$i]['created_at_human'] = Carbon::createFromFormat('Y-m-d H:i:s', $result[$i]['created_at'])->copy()->tz('Asia/Manila')->format('F j, Y');
          $result[$i]['needed_on_human'] = Carbon::createFromFormat('Y-m-d', $result[$i]['needed_on'])->copy()->tz('Asia/Manila')->format('F j, Y');
          $result[$i]['total'] = $this->getTotalBorrowed($result[$i]['account_id']);
          $result[$i]['initial_amount'] = $result[$i]['amount'];
          $result[$i]['amount'] = $amount - $invested['total'];
          $result[$i]['invested'] = $invested['size'];
          $result[$i]['pulling_percentage'] = intval(($result[$i]['pulling'] /  $result[$i]['initial_amount']) * 100);
          $result[$i]['billing_per_month_human'] = $this->billingPerMonth($result[$i]['billing_per_month']);
          $i++;
        }
      }
    	return response()->json(array(
        'data' => sizeof($result) > 0 ? $result : null,
        'size' => sizeof($size)
      ));
    }

    public function retrieveById($id){
      $result = RequestMoney::where('id', '=', $id)->get();
      $result = $this->getAttributes($result);
      return (sizeof($result) > 0) ? $result[0] : null;
    }

    public function getAttributes($result){
      if(sizeof($result) > 0){
        $i = 0;
        foreach ($result as $key) {
          $invested = app($this->investmentClass)->invested($result[$i]['id']);
          $result[$i]['rating'] = app($this->ratingClass)->getRatingByPayload('profile', $result[$i]['account_id']);
          $result[$i]['account'] = $this->retrieveAccountDetails($result[$i]['account_id']);
          $result[$i]['cards'] = app($this->cardClass)->getByParams('account_id', $result[$i]['account_id']);
          $result[$i]['works'] = app($this->workClass)->getByParams('account_id', $result[$i]['account_id']);
          $result[$i]['guarantors'] = app($this->guarantorClass)->getByParams('sender', $result[$i]['account_id']);
          $result[$i]['educations'] = app($this->educationClass)->getByParams('account_id', $result[$i]['account_id']);
          $result[$i]['comakers'] = app($this->comakerClass)->getByParams($result[$i]['account_id'], $result[$i]['id']);
          $result[$i]['created_at_human'] = Carbon::createFromFormat('Y-m-d H:i:s', $result[$i]['created_at'])->copy()->tz('Asia/Manila')->format('F j, Y');
          $result[$i]['needed_on_human'] = Carbon::createFromFormat('Y-m-d', $result[$i]['needed_on'])->copy()->tz('Asia/Manila')->format('F j, Y');
          $result[$i]['total'] = $this->getTotalBorrowed($result[$i]['account_id']);
          $result[$i]['invested'] = $invested['size'];
          $result[$i]['billing_per_month_human'] = $this->billingPerMonth($result[$i]['billing_per_month']);
          $i++;
        }
      }
      return $result;
    }

    public function billingPerMonth($value){
      switch (intval($value)) {
        case 0:
          return 'every end of the month.';
          break;
        case 1:
          return 'twice a month.';
          break;
        case 2: 
          return 'every end of the week';
          break;
      }
    }
    
    public function updateStatus($id){
      RequestMoney::where('id', '=', $id)->update(array(
        'status' => 1,
        'updated_at' => Carbon::now()
      ));
    }

    public function getAmount($requestId){
      $result = RequestMoney::where('id', '=', $requestId)->get();
      return sizeof($result) > 0 ? floatval($result[0]['amount']) : null;
    }   

    public function getTotalBorrowed($accountId){
    	$result = RequestMoney::where('account_id', '=', $accountId)->where('status', '=', 1)->sum('amount');
    	return $result;
    }

    public function total(){
      $result = RequestMoney::where('status', '=', 0)->sum('amount');
      return $result;
    }

    public function approved(){
      $result = RequestMoney::where('status', '=', 1)->sum('amount');
      return $result;
    }

    public function requestStatus($accountId){
      $result = RequestMoney::where('account_id', '=', $accountId)->where('status', '=', 1)->get();
      return (sizeof($result) > 0) ? true : false;
    }

    public function payments($data){

      $result = RequestMoney::where('account_id', '=', $data['account_id'])->where('status', '=', 1)->where('approved_date', '!=', null)->get();
      $result = $this->getAttributes($result);

      if(sizeof($result) > 0){
        $i = 0;
        foreach ($result as $key) {
          $billingDate = $this->manageNextBilling($result[$i]['approved_date'], $result[$i]['billing_per_month']);
          $result[$i]['next_billing_date_human'] = $billingDate->copy()->tz('Asia/Manila')->format('F j, Y');
          $result[$i]['next_billing_date'] = $billingDate->copy()->tz('Asia/Manila')->format('Y-m-d');
          $result[$i]['penalty'] = app($this->penaltyClass)->getTotalPenalty($result[$i]['request_id'], $data['account_id']); 
          $i++;
        }
      }
      return sizeof($result) > 0 ? $result : null;
    }

    public function billingSchedule(){
      $result = RequestMoney::where('status', '=', 1)->where('approved_date', '!=', null)->get();

      $result = $this->getAttributes($result);

      if(sizeof($result) > 0){
        $i = 0;
        foreach ($result as $key) {
          $billingDate = $this->manageNextBilling($result[$i]['approved_date'], $result[$i]['billing_per_month']);
          $result[$i]['next_billing_date_human'] = $billingDate->copy()->tz('Asia/Manila')->format('F j, Y');
          $result[$i]['next_billing_date'] = $billingDate->copy()->tz('Asia/Manila')->format('Y-m-d');
          $result[$i]['send_billing_flag'] = true;
          $i++;
        }
      }
      return $result;
    }

    public function manageNextBilling($approvedDate, $billingPerMonth){
      $days = 0;
      $approvedDate = Carbon::createFromFormat('Y-m-d H:i:s', $approvedDate);
      $currentDate = Carbon::now();
      $diff = $currentDate->diffInDays($approvedDate, false);
      
        // 31, 30
      if($diff > 0){
        if($billingPerMonth == 0){
          return Carbon::createFromFormat('Y-m-d H:i:s', $approvedDate)->addMonth();
        }else if($billingPerMonth == 1){
          return Carbon::createFromFormat('Y-m-d H:i:s', $approvedDate)->addMonth()->subWeeks(2);
        }
      }else{
        if($approvedDate->month == $currentDate->month && $approvedDate->year == $currentDate->year){
          if($billingPerMonth == 0){
            return Carbon::createFromFormat('Y-m-d H:i:s', $approvedDate)->addMonth();
          }else if($billingPerMonth == 1){
            return Carbon::createFromFormat('Y-m-d H:i:s', $approvedDate)->addMonth()->subWeeks(2);
          }
        }else{
          $stringDate = $currentDate->year.'-'.$currentDate->month.'-'.$approvedDate->day;
          if($billingPerMonth == 0){
            return Carbon::createFromFormat('Y-m-d', $stringDate);
          }else if($billingPerMonth == 1){
            return Carbon::createFromFormat('Y-m-d', $stringDate)->subWeeks(2);
          }
          
        }
      }
      if($billingPerMonth == 2){
        return Carbon::now()->endOfWeek()->subDay();
      }
      return null;
    }
}
