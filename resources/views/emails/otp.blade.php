<!-- resources/views/emails/otp.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$status == 'email_verify' ? 'Your OTP Code' : 'Youe Reset Code'}}</title>
</head>
<body>
    <div>
        <img src="{{url('/public/logo.png')}}" alt="">
    </div>
    <p>Dear user,</p>
    <p>Your {{$status== 'email_verify' ? 'OTP code' : 'Reset code'}} is: <strong>{{ $otp }}</strong></p>
    <p>Please use this code to complete your verification. The code is valid for a limited time.</p>
    <p>Thank you!</p>
</body>
</html>
