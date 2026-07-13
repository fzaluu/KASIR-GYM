<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Kasir Gym')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('img/2.png') }}">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            display: flex;
            background-color: #f4f6f9;
            min-height: 100vh;
        }

        .sidebar-kiri {
            width: 260px;
            background-color: #1e293b;
            color: #fff;
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
        }

        .sidebar-logo {
            width: 85px;         
            height: auto;
            object-fit: contain;
            align-self: center;
            filter: drop-shadow(0px 0px 8px rgba(255, 255, 255, 0.25)); 
        }

        .sidebar-kiri h2 {
            font-size: 20px;   
            color: #38bdf8;
            letter-spacing: 1.5px;
            text-align: center;
            font-weight: 700;
            margin-bottom: 30px; 
        }

        .menu-links {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .menu-links a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 14px;
            padding: 12px 18px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .menu-links a.active, .menu-links a:hover {
            background-color: #334155;
            color: #fff;
        }

        .menu-kategori {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 10px 0 5px 10px;
            font-weight: 600;
        }

        .mobile-nav {
            display: none;
            background-color: #1e293b;
            padding: 10px 16px;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0; left: 0; width: 100%;
            z-index: 999;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .mobile-brand {
            display: flex;
            align-items: center;
            gap: 8px; 
        }
        
        .mobile-brand h2 {
            font-size: 16px;
            color: #38bdf8;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .mobile-logo {
            width: 32px; 
            height: auto;
            filter: drop-shadow(0px 0px 4px rgba(255, 255, 255, 0.3));
        }

        .mobile-menu-links {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .mobile-menu-links a, .mobile-dropdown-btn {
            color: #94a3b8;
            text-decoration: none;
            font-size: 13px;
            padding: 8px 12px;
            border-radius: 6px;
            background: none;
            border: none;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .mobile-menu-links a.active, .mobile-dropdown-btn.active {
            background-color: #334155;
            color: #fff;
        }

        .panah-icon {
            display: inline-block;
            transition: transform 0.3s ease;
            transform: rotate(180deg); 
            font-size: 10px;
        }

        .mobile-nav.open .panah-icon {
            transform: rotate(0deg);
        }

        .mobile-dropdown-content {
            max-height: 0;
            overflow: hidden;
            background-color: #111827;
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            transition: max-height 0.3s ease-out;
        }

        .mobile-dropdown-content a {
            color: #94a3b8;
            padding: 14px 24px;
            display: block;
            text-decoration: none;
            font-size: 13px;
            border-bottom: 1px solid #1f2937;
        }

        .mobile-dropdown-content a.active_sub {
            background-color: #1f2937;
            color: #38bdf8;
            font-weight: 600;
        }

        .mobile-nav.open .mobile-dropdown-content {
            max-height: 200px;
        }

        .main-content {
            flex: 1;
            padding: 40px;
            margin-left: 260px;
            display: flex;
            flex-direction: column;
            gap: 24px;
            max-width: 1400px;
            width: calc(100% - 260px);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 15px;
            margin-top: 0;
        }

        .header h1 {
            font-size: 26px;
            color: #0f172a;
        }

        /* TAMBAHAN KITA UNTUK LOGOUT */
        .admin-area { display: flex; align-items: center; gap: 15px; }
        .logout-btn { background: #dc2626; color: #fff; border: none; padding: 5px 12px; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: bold; }

        @media screen and (max-width: 768px) {
            body { flex-direction: column; }
            .sidebar-kiri { display: none; }
            .mobile-nav { display: flex; }
            .main-content { 
                margin-left: 0; 
                padding: 20px; 
                margin-top: 60px;
                width: 100%;
            }
            .header { flex-direction: column; align-items: flex-start; gap: 5px; }
            .header h1 { font-size: 20px; }
        }
    </style>

    @stack('styles')
</head>
<body>

    <div class="sidebar-kiri">
        <img src="{{ asset('img/gym.png') }}" alt="Logo Virgo Gym" class="sidebar-logo">
        <h2>VIRGO GYM</h2>
        
        <div class="menu-links">
        <a href="{{ url('/') }}" class="{{ Request::is('/') ? 'active' : '' }}">Dashboard</a>
        
        <div class="menu-kategori">Manajemen Pengguna</div>
        <a href="{{ route('harian.index') }}" class="{{ Request::is('harian*') ? 'active' : '' }}">Pengunjung Harian</a>
        <a href="{{ route('member.index') }}" class="{{ Request::is('member*') ? 'active' : '' }}">Data Member</a>
        
        {{-- MULAI GERBANG ADMIN --}}
        @if(Auth::check() && Auth::user()->role == 'admin')
            <a href="{{ route('pelatih.index') }}" class="{{ Request::is('pelatih*') ? 'active' : '' }}">Data Pelatih</a>
            <div class="menu-kategori">Keuangan</div>
            <a href="{{ route('transaksi.index') }}" class="{{ Request::is('transaksi*') ? 'active' : '' }}">Catatan Transaksi</a>
        @endif
        {{-- SELESAI GERBANG ADMIN --}}
    </div>
    </div>

    <div class="mobile-nav" id="mobileNavWrapper">
        <div class="mobile-brand">
            <img src="{{ asset('img/gym.png') }}" alt="Logo" class="mobile-logo">
            <h2>VIRGO GYM</h2>
        </div>

        <div class="mobile-menu-links">
            <a href="{{ url('/') }}" class="{{ Request::is('/') ? 'active' : '' }}">Dashboard</a>
            <button class="mobile-dropdown-btn {{ Request::is('harian*') || Request::is('member*') || Request::is('pelatih*') ? 'active' : '' }}" onclick="toggleMobileDropdown()">
                Pengguna <span class="panah-icon">▲</span>
            </button>
            <a href="{{ route('transaksi.index') }}" class="{{ Request::is('transaksi*') ? 'active' : '' }}">Transaksi</a>
        </div>

        <div class="mobile-dropdown-content">
            <a href="{{ route('harian.index') }}" class="{{ Request::is('harian*') ? 'active_sub' : '' }}">Harian</a>
            <a href="{{ route('member.index') }}" class="{{ Request::is('member*') ? 'active_sub' : '' }}">Member</a>
            <a href="{{ route('pelatih.index') }}" class="{{ Request::is('pelatih*') ? 'active_sub' : '' }}">Pelatih</a>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>@yield('page_title')</h1>
            
            @auth
            <div class="admin-area">
                <div style="background: yellow; padding: 10px; color: black; font-weight: bold;">
    Status Role User: {{ Auth::check() ? Auth::user()->role : 'Tidak Login' }}
</div>
                <span>Admin: <strong>{{ Auth::user()->name }}</strong></span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-btn">Logout</button>
                </form>
            </div>
            @endauth
        </div>

        @yield('konten')
    </div>

    <script>
        function toggleMobileDropdown() {
            document.getElementById('mobileNavWrapper').classList.toggle('open');
        }

        window.onclick = function(event) {
            if (!event.target.matches('.mobile-dropdown-btn') && !event.target.matches('.panah-icon')) {
                var wrapper = document.getElementById('mobileNavWrapper');
                if (wrapper.classList.contains('open')) {
                    wrapper.classList.remove('open');
                }
            }
        }
    </script>

    @stack('scripts')
</body>
</html>