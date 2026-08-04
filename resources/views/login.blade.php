@extends('layouts.auth')

@section('title', 'Login')
@section('page_title', 'Halaman Login')

@section('konten_login')
<style>
    /* Reset & Background Layar Penuh */
    body {
        margin: 0;
        padding: 0;
        background: linear-gradient(135deg, #0f172a, #1e3a8a, #0ea5e9);
        background-size: 400% 400%;
        animation: bgMove 12s ease infinite;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    @keyframes bgMove {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* Wrapper utama agar card benar-benar di tengah */
    .login-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100vw;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 9999;
    }

    /* Card Glassmorphism Utama */
    .login-card {
        width: 100%;
        max-width: 420px;
        margin: 0 20px;
        padding: 40px;
        border-radius: 20px;
        background: rgba(15, 23, 42, 0.65) !important;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5) !important;
        color: white;
        box-sizing: border-box;
    }

    .login-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .login-header h4 {
        font-size: 28px;
        font-weight: 700;
        letter-spacing: 0.5px;
        margin: 0 0 8px 0;
        color: #ffffff;
    }

    .login-subtitle {
        font-size: 14px;
        color: #94a3b8;
    }

    .input-group {
        margin-bottom: 20px;
    }

    .input-group label {
        color: #e2e8f0;
        margin-bottom: 8px;
        display: block;
        font-size: 13px;
        font-weight: 600;
    }

    /* Wrapper khusus input password agar icon mata posisinya pas di dalam kotak */
    .password-container {
        position: relative;
        width: 100%;
    }

    .input-group input {
        width: 100%;
        padding: 14px 45px 14px 16px; /* Ruang kanan disiapkan untuk icon mata */
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.08) !important;
        color: white !important;
        outline: none;
        transition: all 0.3s ease;
        font-size: 14px;
        box-sizing: border-box;
    }

    .input-group input::placeholder {
        color: #94a3b8;
    }

    .input-group input:focus {
        background: rgba(255, 255, 255, 0.15) !important;
        border-color: #38bdf8;
        box-shadow: 0 0 15px rgba(56, 189, 248, 0.3);
    }

    /* Styling Icon Mata */
    .toggle-password {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        color: #94a3b8;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.2s;
    }

    .toggle-password:hover {
        color: #38bdf8;
    }

    .toggle-password svg {
        width: 20px;
        height: 20px;
        fill: currentColor;
    }

    .btn-login {
        width: 100%;
        padding: 15px;
        border: none;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 700;
        letter-spacing: 0.5px;
        color: white;
        cursor: pointer;
        background: linear-gradient(135deg, #38bdf8, #2563eb);
        transition: all 0.3s ease;
        margin-top: 10px;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.4);
    }

    .btn-login.loading {
        pointer-events: none;
        background: #2563eb;
        opacity: 0.85;
    }

    .alert-error {
        background: rgba(239, 68, 68, 0.25);
        color: #fca5a5;
        padding: 12px 16px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-size: 13px;
        font-weight: 500;
        border: 1px solid rgba(239, 68, 68, 0.4);
        text-align: center;
        transition: opacity 0.6s ease;
    }
</style>


<div class="login-wrapper">
    <div class="login-card">
        <div class="login-header">
            <h4>VIRGO GYM</h4>
            <div class="login-subtitle">Silakan masuk menggunakan akun kasir/admin</div>
        </div>
        
        @if(session('error'))
            <div class="alert-error" id="errorAlert">{{ session('error') }}</div>
        @endif

        <form action="{{ route('login.proses') }}" method="POST">
            @csrf
            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Masukkan username Anda" required autocomplete="off">
            </div>
            
            <div class="input-group">
                <label>Password</label>
                <div class="password-container">
                    <input type="password" name="password" id="passwordInput" placeholder="Masukkan password Anda" required>
                    <!-- Tombol / Icon Mata -->
                    <button type="button" class="toggle-password" id="togglePasswordBtn" title="Tampilkan Password">
                        <!-- Icon Mata Tertutup (Default) -->
                        <svg id="eyeIconClosed" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                        <!-- Icon Mata Terbuka (Disembunyikan dulu) -->
                        <svg id="eyeIconOpen" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="display: none;"><path d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46C3.08 8.3 1.78 10.02 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.02-.17c0-1.66-1.34-3-3-3l-.17.02z"/></svg>
                    </button>
                </div>
            </div>
            
            <button type="submit" class="btn-login" id="loginBtn">
                <span class="btn-text">LOGIN SISTEM</span>
            </button>
        </form>
    </div>
</div>

<script>
    // 1. Script Toggle Show / Hide Password
    const toggleBtn = document.getElementById("togglePasswordBtn");
    const passwordInput = document.getElementById("passwordInput");
    const eyeClosed = document.getElementById("eyeIconClosed");
    const eyeOpen = document.getElementById("eyeIconOpen");

    toggleBtn.addEventListener("click", function () {
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            eyeClosed.style.display = "none";
            eyeOpen.style.display = "block";
            toggleBtn.setAttribute("title", "Sembunyikan Password");
        } else {
            passwordInput.type = "password";
            eyeClosed.style.display = "block";
            eyeOpen.style.display = "none";
            toggleBtn.setAttribute("title", "Tampilkan Password");
        }
    });

    // 2. Script Notif Error Hilang Otomatis dalam 6 Detik
    document.addEventListener("DOMContentLoaded", function() {
        const errorAlert = document.getElementById("errorAlert");
        if (errorAlert) {
            setTimeout(function() {
                errorAlert.style.opacity = "0";
                setTimeout(function() {
                    errorAlert.style.display = "none";
                }, 600);
            }, 6000); // 6 detik
        }
    });

    // 3. Script Efek Loading Tombol saat Login
    document.querySelector("form").addEventListener("submit", function(e){
        const btn = document.getElementById("loginBtn");
        btn.classList.add("loading");
        btn.innerHTML = `
            <svg width="20" height="20" viewBox="0 0 50 50" style="vertical-align:middle;margin-right:8px;">
                <circle cx="25" cy="25" r="20" fill="none" stroke="white" stroke-width="5" stroke-linecap="round" stroke-dasharray="31.4 31.4">
                    <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="0.8s" values="0 25 25;360 25 25"/>
                </circle>
            </svg>
            Memproses...
        `;
    });
</script>
@endsection