<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Work extends APIModel
{
    protected $table = 'works';
    protected $fillable = ['acoount_id', 'company_name','position','location','work_description','year_started','year_ended','month_started','month_ended'];
}
