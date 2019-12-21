<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RequestImage;
class RequestImageController extends APIController
{
  function __construct(){
    $this->model = new RequestImage();
  }

  public function getByParams($column, $value){
    $result = RequestImage::where($column, '=', $value)->get();
    return (sizeof($result) > 0) ? $result[0] : null;
  }
}
