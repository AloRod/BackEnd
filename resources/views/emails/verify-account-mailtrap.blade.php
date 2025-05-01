<!DOCTYPE html>
<html>
<head>
    <title>Activate your account</title>
    <style>
        /* Agrega estilos básicos para el correo */
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .container { padding: 20px; }
        .button { display: inline-block; padding: 10px 20px; font-size: 16px; color: #fff !important; background-color: #007bff; text-decoration: none; border-radius: 5px; }
        p { margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <p>Hello {{ $recipientName }}!</p>
        <p>Please click the button below to activate your account for {{ $appName }}.</p>
        <p>
            <a href="{{ $verificationUrl }}" class="button">Activate account</a>
        </p>
        <p>If you did not create an account, no further action is required.</p>
        <p>Regards,<br>{{ $appName }}</p>
        <hr>
        <p style="font-size: 0.8em; color: #777;">
            If you're having trouble clicking the "Activate account" button, copy and paste the URL below into your web browser:
            <br>
            <a href="{{ $verificationUrl }}" style="word-break: break-all;">{{ $verificationUrl }}</a>
        </p>
    </div>
</body>
</html>