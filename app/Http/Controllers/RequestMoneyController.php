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
    public $bookmarkClass = 'App\Http\Controllers\BookmarkController';
    public $requestLocationClass = 'App\Http\Controllers\RequestLocationController';
    public $requestImageClass = 'App\Http\Controllers\RequestImageController';
    public $requestPeerClass = 'App\Http\Controllers\RequestPeerController';
    public $ledgerClass = 'App\Http\Controllers\LedgerController';
    
    function __construct(){  
    	$this->model = new RequestMoney();
      $this->notRequired = array(
        'approved_date', 'months_payable', 'interest', 'reason', 'billing_per_month'
      );
    }

    public function create(Request $request){
    	$data = $request->all();
    	$data['code'] = $this->generateCode();
      $data['status'] = 0;
    	$this->model = new RequestMoney();
    	$this->insertDB($data);
      if(intval($data['type']) > 100){
        // comaker
        // images
        $getID = RequestMoney::where('code', '=', $data['code'])->get();
        $userExist = Account::where('email', '=', $data['comaker'])->get();
        if(sizeof($userExist) > 0){
          $comaker = $userExist[0]->id;
          app($this->comakerClass)->addToComaker($data['account_id'], $getID[0]->id, $comaker);
          $requestMoney = RequestMoney::where('id', '=', $this->response['data'])->get();
          $parameter = array(
            'to' => $comaker,
            'from' => $data['account_id'],
            'payload' => 'comaker',
            'payload_value' => $getID[0]->id,
            'route' => '/requests/'.$requestMoney[0]['code'],
            'created_at' => Carbon::now()
          );
          app($this->notificationClass)->createByParams($parameter);
        }
        if(sizeof($data['images']) > 0){
          app($this->requestImageClass)->insert($data['images'], $this->response['data']);
        }
      }else{
        // add location
        $data['location']['request_id'] = $this->response['data'];
        $data['location']['created_at'] = Carbon::now();
        app($this->requestLocationClass)->insert($data['location']);
      }
      $this->response['data'] = $data['code'];
    	return $this->response();
    }

    public function manageRequestByThread(Request $request){
      $data = $request->all();
      $error = null;
      $responseData = null;
      $result = RequestMoney::where('code', '=', $data['code'])->where('account_id', '=', $data['account_id'])->where('status', '=', 0)->get();
      if(sizeof($result) > 0){
        $result = $result[0];
        $result['account'] = $this->retrieveAccountDetails($result['account_id']);
        $peerApproved = app($this->requestPeerClass)->getApprovedByParams('request_id', $result['id']);
        if($peerApproved != null){
          $response = $this->processPaymentByType($result, $peerApproved);
          if($response == true){
            // update status of the requet
            RequestMoney::where('code', '=', $data['code'])->update(array(
              'status' => 2,
              'updated_at' => Carbon::now()
            ));
            $responseData = true;
          }else{
            $error = 'Unabled to process the payment!';
          }
        }else{
          $error = 'No peer was selected! Invalid accessed';
        }
      }else{
        $error = 'Request was not found! Invalid accessed!';
      }

      return response()->json(array(
        'error' => $error,
        'data' => $responseData,
        'timestamps' => Carbon::now()
      ));
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

    public function processPaymentByType($request, $charge){
      $data = null;
      $email = null;
      $receiverData = null;
      $receiverEmail = null;
      $notification = null;
      switch (intval($request['type'])) {
        case 1:
          $data = array(
            'account_id'    => $request['account_id'],
            'amount'        => (doubleval($request['amount']) * -1),
            'currency'      => $request['currency'],
            'description'   => 'Sending from Money Transfer',
            'payload'       => 'request',
            'payload_value' => $request['code']
          );
          $email = array(
            'username'      => $charge['account']['username'],
            'subject'       => 'Sending from Money Transfer',
            'status'        => 'to'
          );
          $receiverData = array(
            'account_id'    => $charge['account_id'],
            'amount'        => (doubleval($request['amount']) * 1),
            'currency'      => $request['currency'],
            'description'   => 'Receiving from Money Transfer',
            'payload'       => 'request',
            'payload_value' => $request['code']
          );
          $receiverEmail = array(
            'username'      => $request['account']['username'],
            'subject'       => 'Receiving from Money Transfer',
            'status'        => 'from'
          );
          break;
        case 2:
          // withdrawal
          # code...
          break;
        case 3:
          // Deposit
          # code...
          break;
      }
      $notification = array(
        'to'    => $request['account_id'],
        'from'  => $charge['account_id'],
        'payload' => 'ledger',
        'payload_value' => $request['code'],
        'route' => '/dashboard',
        'created_at' => Carbon::now()
      );
      if($data != null && $email != null){
        // process sender
        $ledger = app($this->ledgerClass)->processPayment($data, $email, $notification);
        if($ledger){
          // process sender charge
          $data['amount'] = (doubleval($charge['charge']) * -1);
          $data['currency'] = $charge['currency'];
          $data['description'] = $data['description'].' Charge';
          $email['subject'] = $email['subject'].' Charge';
          $ledger = app($this->ledgerClass)->processPayment($data, $email, $notification);
          if($ledger){
            // process receiver
            $notification['to'] = $charge['account_id'];
            $notification['from'] = $request['account_id'];
            $ledger = app($this->ledgerClass)->processPayment($receiverData, $receiverEmail, $notification);
            if($ledger){
              // process receiver charge
              $charges = (doubleval($charge['charge']) * 1) * env('CHARGE_RATE_PROCESSOR');
              $receiverData['amount'] = $charges;
              $receiverData['currency'] = $charge['currency'];
              $receiverData['description'] = $data['description'];
              $receiverEmail['subject'] = $email['subject'];
              $ledger = app($this->ledgerClass)->processPayment($receiverData, $receiverEmail, $notification);

              // process receiver charge for payhiram
              $notification['to'] = env('PAYHIRAM_ACCOUNT');
              $notification['from'] = $charge['account_id'];
              $charges = (doubleval($charge['charge']) * 1) - $charges;
              $receiverData['account_id'] = env('PAYHIRAM_ACCOUNT'); // account_id of payhiram
              $receiverData['amount'] = $charges;
              $receiverData['currency'] = $charge['currency'];
              $receiverData['description'] = $data['description'];
              $receiverEmail['subject'] = $email['subject'];
              $ledger = app($this->ledgerClass)->processPayment($receiverData, $receiverEmail, $notification);
            }
          }
        }
        return true;
      }else{
        return false;
      }
    }

    public function retrieve(Request $request){
    	$data = $request->all();
      $result = array();
      $response = array();
      if($data['value'] != null){
        $result = RequestMoney::where('status', '=', 0)->where($data['column'], 'like', $data['value'])->limit(intval($data['limit']))->offset(intval($data['offset']))->orderBy($data['sort']['column'], $data['sort']['value'])->get();
      }else{
        $result = RequestMoney::where('status', '=', 0)->where($data['column'], 'like', $data['value'])->orWhere('type', '<=', 100)->limit(intval($data['limit']))->offset(intval($data['offset']))->orderBy($data['sort']['column'], $data['sort']['value'])->get();
      }
      
      $size =  RequestMoney::where('status', '=', 0)->get();
      if(sizeof($result) > 0){
        $i = 0;
        foreach ($result as $key) {
          $peerApproved = app($this->requestPeerClass)->checkIfApproved($result[$i]['id']);
          if($peerApproved == false || $data['value'] == $result[$i]['code'].'%'){
            $invested = app($this->investmentClass)->invested($result[$i]['id']);
            $amount = floatval($result[$i]['amount']);
            $result[$i]['location'] = app($this->requestLocationClass)->getByParams('request_id', $result[$i]['id']);
            $result[$i]['peers'] = app($this->requestPeerClass)->getByParams('request_id', $result[$i]['id']);
            $result[$i]['images'] = app($this->requestImageClass)->getByParams('request_id', $result[$i]['id']);
            $result[$i]['rating'] = app($this->ratingClass)->getRatingByPayload('profile', $result[$i]['account_id']);
            $result[$i]['pulling'] = app($this->pullingClass)->getTotalByParams('request_id', $result[$i]['id']);
            $result[$i]['account'] = $this->retrieveAccountDetails($result[$i]['account_id']);
            $result[$i]['works'] = app($this->workClass)->getByParams('account_id', $result[$i]['account_id']);
            $result[$i]['cards'] = app($this->cardClass)->getByParams('account_id', $result[$i]['account_id'], $data['type']);
            $result[$i]['guarantors'] = app($this->guarantorClass)->getByParams('sender', $result[$i]['account_id']);
            $result[$i]['educations'] = app($this->educationClass)->getByParams('account_id', $result[$i]['account_id']);
            $result[$i]['comakers'] = app($this->comakerClass)->getByParams($result[$i]['account_id'], $result[$i]['id']);
            $result[$i]['created_at_human'] = Carbon::createFromFormat('Y-m-d H:i:s', $result[$i]['created_at'])->copy()->tz('Asia/Manila')->format('F j, Y H:i A');
            $result[$i]['needed_on_human'] = Carbon::createFromFormat('Y-m-d', $result[$i]['needed_on'])->copy()->tz('Asia/Manila')->format('F j, Y');
            $result[$i]['total'] = $this->getTotalBorrowed($result[$i]['account_id']);
            $result[$i]['initial_amount'] = $result[$i]['amount'];
            $result[$i]['amount'] = $amount - $invested['total'];
            $result[$i]['invested'] = $invested['size'];
            $result[$i]['pulling_percentage'] = intval(($result[$i]['pulling'] /  $result[$i]['initial_amount']) * 100);
            $result[$i]['billing_per_month_human'] = $this->billingPerMonth($result[$i]['billing_per_month']);
            $result[$i]['bookmark'] = (app($this->bookmarkClass)->checkIfExist($data['account_id'], $result[$i]['id']) == null) ? false : true;
            $response[] = $result[$i];
          }  
          $i++;
        }
      }
    	return response()->json(array(
        'data' => sizeof($response) > 0 ? $response : null,
        'size' => sizeof($size),
        'ledger' => app($this->ledgerClass)->retrievePersonal($data['account_id'])
      ));
    }

    public function retrieveById($id, $type = null){
      $result = RequestMoney::where('id', '=', $id)->get();
      $result = $this->getAttributes($result, $type);
      return (sizeof($result) > 0) ? $result[0] : null;
    }

    public function getByParams($column, $value){
      $result = RequestMoney::where($column, '=', $value)->get();
      return (sizeof($result) > 0) ? $result[0] : null;
    }

    public function getAttributes($result, $type = null){
      if(sizeof($result) > 0){
        $i = 0;
        foreach ($result as $key) {
          $invested = app($this->investmentClass)->invested($result[$i]['id']);
          $result[$i]['location'] = app($this->requestLocationClass)->getByParams('request_id', $result[$i]['id']);
          $result[$i]['images'] = app($this->requestImageClass)->getByParams('request_id', $result[$i]['id']);
          $result[$i]['rating'] = app($this->ratingClass)->getRatingByPayload('profile', $result[$i]['account_id']);
          $result[$i]['account'] = $this->retrieveAccountDetails($result[$i]['account_id']);
          $result[$i]['cards'] = app($this->cardClass)->getByParams('account_id', $result[$i]['account_id'], $type);
          $result[$i]['works'] = app($this->workClass)->getByParams('account_id', $result[$i]['account_id']);
          $result[$i]['guarantors'] = app($this->guarantorClass)->getByParams('sender', $result[$i]['account_id']);
          $result[$i]['educations'] = app($this->educationClass)->getByParams('account_id', $result[$i]['account_id']);
          $result[$i]['comakers'] = app($this->comakerClass)->getByParams($result[$i]['account_id'], $result[$i]['id']);
          $result[$i]['created_at_human'] = Carbon::createFromFormat('Y-m-d H:i:s', $result[$i]['created_at'])->copy()->tz('Asia/Manila')->format('F j, Y H:i A');
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
