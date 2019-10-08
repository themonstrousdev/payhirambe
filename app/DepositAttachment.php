<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DepositAttachment extends APIModel
{
    protected $table = 'deposit_attachments';
    protected $fillable = ['deposit_id', 'file'];
}
