<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Education extends APIModel
{
  protected $table = 'educations';
  protected $fillable = ['account_id', 'school', 'degree', 'field_of_study', 'year_started', 'year_ended', 'month_started', 'month_ended'];
}
