<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bookmark;
class BookmarkController extends APIController
{
    function __construct(){
      $this->model = new Bookmark();
    }

    public function create(Request $request){
      $data = $request->all();
      $id = $this->checkIfExist($data['account_id'], $data['request_id']);
      if($id != null){
        $data['id'] = $id;
        $this->model = new Bookmark();
        $this->updateDB($data);
      }else{
        $this->model = new Bookmark();
        $this->insertDB($data);
      }
      return $this->response();
    }

    public function checkIfExist($accountId, $requestId){
      $result = Bookmark::where('account_id', '=', $accountId)->where('request_id', '=', $requestId)->get();

      return (sizeof($result) > 0) ? $result[0]['id'] : null;
    }
}
