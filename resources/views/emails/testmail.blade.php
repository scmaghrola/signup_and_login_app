<!DOCTYPE html>
<html>
<head>
    <title>OTP Verification</title>
</head>
<body>
    <h2>Hello, {{ $email }}</h2>
    <p>Your OTP for signup is:</p>
    <h1 style="color:blue;">{{ $otp }}</h1>
    <p>This OTP will expire in 5 minutes.</p>
</body>
</html>
