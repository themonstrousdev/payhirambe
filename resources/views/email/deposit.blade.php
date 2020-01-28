@component('email.header')
@endcomponent
<span class="text" style="text-align: center;">
    <h1>
      Hello {{$user->username}}!
      <br>
      You have created a deposit transaction with the amount of {{$details['currency']}} {{$details['amount']}} via {{$details['bank']}} on {{$date}}
      <br>
      Status is on {{$details['status']}}.
    </h1>
</span>
<span class="text" style="text-align: center;">
    To continue with the transaction, Please click on the link below and carefully follow the instructions:
    <!-- Link here -->
     <a href="{{env('APP_FRONT_END_URL')}}/paymentConfirmation/{{$user->email}}/{{$user->code}}/{{$details['code']}}">Continue</a>
</span>
<span class="text" style="text-align: center;">
    Transaction id: {{$details['code']}}
    <br>
    <br>
</span>
<span class="text" style="text-align: center;">
    If you did not make this action, please <a href="{{env('APP_FRONT_END_URL')}}/reset_password/{{$user->username}}/{{$user->code}}">reset</a> your password to secure your account and reply to this message to notify us.
</span>
@component('email.footer')
@endcomponent

