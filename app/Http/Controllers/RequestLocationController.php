<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RequestLocation;
class RequestLocationController extends APIController
{
    function __construct(){
      $this->model = new RequestLocation();
    }

    public function getByParams($column, $value){
      $result = RequestLocation::where($column, '=', $value)->get();
      return (sizeof($result) > 0) ? $result[0] : null;
    }

    public function insert($data){
      RequestLocation::create($data);
      return true;
    }
}
