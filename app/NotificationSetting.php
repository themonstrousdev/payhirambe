<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends APIModel
{
    protected $table = 'notification_settings';
    protected $fillable = ['code', 'account_id', 'email_login', 'email_otp', 'sms_login', 'sms_otp'];
}
