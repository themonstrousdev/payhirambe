<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Increment\Messenger\Models\MessengerGroup;
use Increment\Messenger\Models\MessengerMember;
use Increment\Messenger\Models\MessengerMessage;
use Carbon\Carbon;
use Increment\Account\Models\Account;
use Illuminate\Support\Facades\DB;
use App\Events\Message;
class MessengerGroupController extends APIController
{
    public $notificationClass = 'Increment\Common\Notification\Http\NotificationController';
    public $requestValidationClass = 'App\Http\Controllers\RequestValidationController';
    public $ratingClass = 'Increment\Common\Rating\Http\RatingController';
    public $requestClass = 'App\Http\Controllers\RequestMoneyController';
    public $requestPeerClass = 'App\Http\Controllers\RequestPeerController';
    function __construct(){
      $this->model = new MessengerGroup();
    }

    public function create(Request $request){
      $data = $request->all();

      $creator = intval($data['creator']);
      $memberData = intval($data['member']);
      $this->model = new MessengerGroup();
      $insertData = array(
        'account_id'  => $creator,
        'title'       => $data['title'],
        'payload'     => $data['payload'] 
      );
      $this->insertDB($insertData);
      $id = intval($this->response['data']);
      if($this->response['data'] > 0){
        $member = new MessengerMember();
        $member->messenger_group_id = $id;
        $member->account_id = $creator;
        $member->status = 'admin';
        $member->created_at = Carbon::now();
        $member->save();

        $member = new MessengerMember();
        $member->messenger_group_id = $id;
        $member->account_id = $memberData;
        $member->status = 'member';
        $member->created_at = Carbon::now();
        $member->save();

        $parameter = array(
          'to' => $memberData,
          'from' => $creator,
          'payload' => 'thread',
          'payload_value' => $id,
          'route' => '/thread/'.$data['title'],
          'created_at' => Carbon::now()
        );
        app($this->notificationClass)->createByParams($parameter);
      }
      return $this->response();
    }

    public function retrieve(Request $request){
      $data = $request->all();
      $code = $data['code'];
      $accountId = $data['account_id'];
      $existed = array();
      $flag = false;
      $active = 0;
      $response = array();
      $result = DB::table('messenger_members as T1')
        ->join('messenger_groups as T2', 'T2.id', '=', 'T1.messenger_group_id')
        ->where('T1.account_id', '=', $accountId)
        ->where('T2.payload', '!=', 'support')
        ->orderBy('T2.updated_at', 'DESC')
        ->select('T2.*')
        ->get();
      $result = json_decode($result, true);
      if(sizeof($result) > 0){
        $i = 0;
        foreach ($result as $key) {
          $result[$i]['account_id'] = intval($result[$i]['account_id']);
          $result[$i]['account_details'] = $this->retrieveAccountDetails($result[$i]['account_id']);
          $result[$i]['members'] = $this->getMembers($result[$i]['id'], null);
          $members = $result[$i]['members']['result'];
          if(sizeof($members) > 0){
            if(intval($data['account_id']) == intval($members[0]['account_id'])){
              $result[$i]['title'] = $this->retrieveAccountDetails($members[1]['account_id']);
            }else{
              $result[$i]['title'] = $this->retrieveAccountDetails($result[$i]['account_id']);
            }
          }else{
            $result[$i]['title'] = $this->retrieveAccountDetails($result[$i]['account_id']);
          }
          $result[$i]['validations'] = app($this->requestValidationClass)->getByParams('request_id', $result[$i]['payload']);
          $result[$i]['request'] = app($this->requestClass)->getByParams('id', $result[$i]['payload']);
          $result[$i]['peer'] = app($this->requestPeerClass)->getApprovedByParams('request_id', $result[$i]['payload']);
          $result[$i]['thread'] = $key['title'];
          $existed[] = $result[$i]['account_id'];
          $result[$i]['rating'] = app($this->ratingClass)->getByParams($accountId, 'request', $result[$i]['payload']);
          $result[$i]['new'] = false;
          if($key['title'] == $code){
            $active = $i;
            $result[$i]['flag'] = true;
          }else{
            $result[$i]['flag'] = false;
          }
          $i++;
        }
      }
      $accounts = null;
      return response()->json(array(
        'data'  => (sizeof($result) > 0) ? $result : null,
        'accounts'  => $accounts,
        'active'  => $active,
        'error' => null,
        'timestamps'  => Carbon::now()
      ));
    }

    public function getMembers($messengerGroupId, $username){
      $result = MessengerMember::where('messenger_group_id', '=', $messengerGroupId)->get();
      $flag = false;
      if(sizeof($result) > 0){
        $i = 0;
        foreach ($result as $key) {
          $account = $this->retrieveAccountDetails($result[$i]['account_id']);
          $result[$i]['account_details'] = $account;
          if($account['username'] == $username){
            $flag = true;
          }
          $i++;
        }
      }
      return (sizeof($result) > 0) ? array(
        'result' => $result,
        'exist_username' => $flag
      ) : null;
    }

    public function getMemberExisted($messengerGroupId){
      $result = MessengerMember::where('messenger_group_id', '=', $messengerGroupId)->where('status', '=', 'member')->get();
      return (sizeof($result) > 0) ? $result[0]['account_id'] : null;
    }

    public function getTitle($messengerGroupId){
      $result = MessengerMember::where('messenger_group_id', '=', $messengerGroupId)->where('status', '=', 'member')->get();
      $title = null;
      if(sizeof($result) > 0){
          $title = $this->retrieveAccountDetails($result[0]['account_id']);
      }
      return ($title) ? $title : null;
    }

    public function getPartner($username){
      $accounts = null;
      $accounts = Account::where('username', '=', $username)->where('account_type', '=', 'PARTNER')->get();
      if(sizeof($accounts) > 0){
        $i = 0;
        foreach ($accounts as $key) {
          $accounts[$i]['title'] = $this->retrieveAccountDetails($accounts[$i]['id']);
          $accounts[$i]['flag'] = true;
          $accounts[$i]['new'] = true;
          $i++;
        }
      }
      return (sizeof($accounts) > 0) ? $accounts : null;
    }

}
