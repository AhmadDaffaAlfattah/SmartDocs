<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SmartDocs</title>
    <link rel="icon" href="{{ asset('images/smartdocs2.png') }}" type="image/png">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-container { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align: center; }
        .logo { width: 80px; margin-bottom: 20px; }
        .title { font-size: 24px; font-weight: 600; color: #333; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; text-align: left; }
        .form-group label { display: block; margin-bottom: 8px; font-size: 14px; color: #555; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box; }
        .form-group input:focus { border-color: #0066cc; outline: none; }
        .btn-login { width: 100%; padding: 12px; background-color: #0066cc; color: white; border: none; border-radius: 4px; font-size: 16px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn-login:hover { background-color: #0052a3; }
        .error-message { color: #dc3545; font-size: 14px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="{{ asset('images/smartdocs2.png') }}" alt="Logo" class="logo">
        <div class="title">Login SmartDocs</div>
        
        @if ($errors->any())
            <div class="error-message">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="name">Username</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus placeholder="Username">
            </div>
            <div class="form-group" style="position: relative;">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="********" style="padding-right: 40px;">
                <span onclick="togglePassword()" style="position: absolute; right: 10px; top: 38px; cursor: pointer; color: #666;">
                    <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </span>
            </div>
            <button type="submit" class="btn-login">Login</button>

            <script>
                function togglePassword() {
                    const passwordInput = document.getElementById('password');
                    const eyeIcon = document.getElementById('eyeIcon');
                    
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        // Change to eye-off icon
                        eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
                    } else {
                        passwordInput.type = 'password';
                        // Change back to eye icon
                        eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
                    }
                }
            </script>
        </form>
    </div>
</body>
</html>
