<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bookmark;
use Carbon\Carbon;
class BookmarkController extends APIController
{

    public $requestClass = 'App\Http\Controllers\RequestMoneyController'; 
    function __construct(){
      $this->model = new Bookmark();
    }

    public function create(Request $request){
      $data = $request->all();
      $bookmark = $this->checkIfExist($data['account_id'], $data['request_id']);
      if($bookmark != null && $bookmark['deleted_at'] == null){
        Bookmark::where('id', '=', $bookmark['id'])->update(array(
          'deleted_at' => Carbon::now()
        ));
        $this->response['data'] = true;
      }else if($bookmark != null && $bookmark['deleted_at'] != null){
        Bookmark::where('id', '=', $bookmark['id'])->update(array(
          'deleted_at' => null
        ));
        $this->response['data'] = true;
      }else{
        $this->model = new Bookmark();
        $this->insertDB($data);
      }
      return $this->response();
    }

    public function checkIfExist($accountId, $requestId){
      $result = Bookmark::where('account_id', '=', $accountId)->where('request_id', '=', $requestId)->get();
      return (sizeof($result) > 0) ? $result[0] : null;
    }

    public function retrieve(Request $request){
      $data = $request->all();
      $this->model = new Bookmark();
      $this->retrieveDB($data);
      $result = $this->response['data'];
      if(sizeof($result) > 0){
        $i = 0;
        foreach ($result as $key) {
          $this->response['data'][$i]['request'] = app($this->requestClass)->retrieveById($result[$i]['request_id']);
          $i++;
        }
      }
      return $this->response();
    }
}
