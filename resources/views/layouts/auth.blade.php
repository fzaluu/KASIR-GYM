<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Virgo Gym</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('img/2.png') }}">

    <style>
        body { 
            margin: 0; 
            padding: 0;
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            background: linear-gradient(135deg, #0f172a, #1e3a8a, #0ea5e9);
        }
        .login-box { 
            width: 100%; 
            /* Hapus background putih, box-shadow, dan padding bawaan agar tidak dobel */
            background: transparent !important; 
            box-shadow: none !important; 
            padding: 0; 
        }
    </style>
</head>
<body>
    <div class="login-box">
        @yield('konten_login')
    </div>
</body>
</html>