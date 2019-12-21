<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RequestImage;
use Carbon\Carbon;
class RequestImageController extends APIController
{
  function __construct(){
    $this->model = new RequestImage();
  }

  public function getByParams($column, $value){
    $result = RequestImage::where($column, '=', $value)->get();
    return (sizeof($result) > 0) ? $result : null;
  }

  public function insert($images, $requestId){
    $i = 0;
    foreach ($images as $key) {
      $images[$i]['request_id'] = $requestId;
      $images[$i]['created_at'] = Carbon::now();
      $i++;
    }
    $result = RequestImage::insert($images);
    return ($result) ? true : false;
  }
}
