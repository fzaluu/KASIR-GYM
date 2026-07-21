@extends('layouts.auth')

@section('title', 'Login - Virgo Gym')
@section('page_title', 'Halaman Login')

@section('konten_login')
<style>
    .login-container {
        display: flex;
        justify-content: center;
        padding-top: 50px;
    }
    .login-card {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(240, 40, 40, 0.1);
        width: 100%;
        max-width: 400px;
    }
    .login-card h4 {
        margin-bottom: 20px;
        color: #1e293b;
        text-align: center;
    }
    .input-group {
        margin-bottom: 15px;
    }
    .input-group label {
        display: block;
        margin-bottom: 5px;
        font-size: 14px;
        font-weight: 600;
        color: #475569;
    }
    .input-group input {
        width: 100%;
        padding: 10px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        outline: none;
    }
    .btn-login {
        width: 100%;
        padding: 12px;
        background-color: #0ea5e9;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }
    .btn-login:hover { background-color: #0284c7; }
    .alert-error {
        background: #fee2e2;
        color: #b91c1c;
        padding: 10px;
        border-radius: 6px;
        margin-bottom: 15px;
        font-size: 13px;
    }
</style>

<div class="login-container">
    <div class="login-card">
        <h4>Masuk ke Sistem</h4>
        
        @if(session('error'))
            <div class="alert-error">{{ session('error') }}</div>
        @endif

        <form action="{{ route('login.proses') }}" method="POST">
            @csrf
            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn-login">LOGIN</button>
        </form>
    </div>
</div>
@endsection