<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Traffic Control & Dispatch System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url("{{ asset('inspinia/img/gallery/background.jfif') }}") no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }
        .login-card {
            width: 380px;
            padding: 35px 30px;
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.15); /* more transparent */
            backdrop-filter: blur(12px); /* stronger glass effect */
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 25px rgba(0,0,0,0.6);
            text-align: center;
            color: #fff;
        }
        .logo {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #f8f9fa;
            text-shadow: 0px 0px 6px rgba(0,0,0,0.7);
        }
        .form-label {
            font-weight: 500;
            text-align: left;
            display: block;
            color: #f1f1f1;
        }
        .form-control {
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.8);
        }
        .form-control:focus {
            box-shadow: 0 0 8px rgba(13,110,253,0.8);
            border-color: #0d6efd;
        }
        .btn-login {
            background-color: #0d6efd;
            color: #fff;
            font-weight: 500;
            border-radius: 8px;
            padding: 10px;
            border: none;
            box-shadow: 0px 4px 10px rgba(13,110,253,0.4);
        }
        .btn-login:hover {
            background-color: #0b5ed7;
            box-shadow: 0px 6px 12px rgba(13,110,253,0.6);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo">🚦 Traffic Control & Dispatch System</div>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3 text-start">
                <label class="form-label">Email address</label>
                <input type="email" name="email" class="form-control" required autofocus>
            </div>
            <div class="mb-3 text-start">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <!-- Hidden location inputs -->
            <input type="hidden" id="latitude" name="latitude">
            <input type="hidden" id="longitude" name="longitude">

            <button type="submit" class="btn btn-login w-100">Login</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                });
            }
        });
    </script>
</body>
</html>
