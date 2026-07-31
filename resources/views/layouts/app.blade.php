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
            background-color: #0f172a;
            min-height: 100vh;
            color: #e2e8f0;
        }

        /* SIDEBAR KIRI */
        .sidebar-kiri {
            width: 260px;
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            color: #fff;
            padding: 24px 16px;
            display: flex;
            flex-direction: column;
            height: 100vh; /* Menggunakan 100vh presisi */
            position: fixed;
            left: 0;
            top: 0;
            z-index: 100;
        }

        .sidebar-top-section {
            display: flex;
            flex-direction: column;
        }

        /* BRAND LOGO - DIPERBESAR & JARAK DIPERJATUH KE DASHBOARD DIPERKETAT */
        .sidebar-brand-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 5px 0 12px 0; /* Jarak padding dikurangi */
            margin-bottom: 12px; /* Jarak ke menu Dashboard diperdekat */
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .sidebar-logo {
            width: 100px;  /* Logo makin besar & jelas */
            height: 100px;
            object-fit: contain;
            margin-bottom: 4px;
            filter: drop-shadow(0px 0px 12px rgba(56, 189, 248, 0.5)); 
        }

        .sidebar-brand-text h2 {
            font-size: 19px; /* Ukuran teks diperbesar sedikit */
            color: #fff;
            letter-spacing: 1px;
            font-weight: 800;
        }

        .menu-links {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .menu-links a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 14px;
            padding: 12px 14px;
            border-radius: 10px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .menu-links a:hover {
            transform: translateX(4px);
        }

        .menu-links a.active, .menu-links a:hover {
            background: rgba(37, 99, 235, 0.18);
            color: #60a5fa;
            border: 1px solid rgba(37, 99, 235, 0.3);
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.15);
        }

        .menu-kategori {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 16px 0 6px 10px;
            font-weight: 600;
        }

        /* CARD USER PROFIL DIPINDAHKAN AGAR MEMBERIKAN SPACE DI ATASNYA */
        .sidebar-user-card {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            padding: 14px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 30px; /* Memberikan space kosong antara Catatan Transaksi & Card Logout */
        }

        .user-profile-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar-badge {
            width: 38px;
            height: 38px;
            background: rgba(37, 99, 235, 0.25);
            color: #60a5fa;
            border: 1px solid rgba(37, 99, 235, 0.4);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }

        .user-detail-text h4 {
            font-size: 13px;
            color: #fff;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 140px;
        }

        .user-detail-text span {
            font-size: 11px;
            color: #38bdf8;
            text-transform: capitalize;
        }

        .sidebar-logout-btn {
            background: transparent;
            color: #94a3b8;
            border: 1px solid rgba(255, 255, 255, 0.15);
            width: 100%;
            padding: 8px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .sidebar-logout-btn:hover {
            background: rgba(220, 38, 38, 0.15);
            color: #f87171;
            border-color: rgba(220, 38, 38, 0.4);
        }


        /* MOBILE NAV */
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

        .mobile-brand { display: flex; align-items: center; gap: 8px; }
        .mobile-brand h2 { font-size: 16px; color: #38bdf8; font-weight: 700; }
        .mobile-logo { width: 36px; height: auto; }
        .mobile-menu-links { display: flex; gap: 8px; align-items: center; }
        .mobile-menu-links a, .mobile-dropdown-btn { color: #94a3b8; text-decoration: none; font-size: 13px; padding: 8px 12px; border-radius: 6px; background: none; border: none; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 6px; }
        .mobile-menu-links a.active, .mobile-dropdown-btn.active { background-color: #334155; color: #fff; }
        .panah-icon { display: inline-block; transition: transform 0.3s ease; transform: rotate(180deg); font-size: 10px; }
        .mobile-nav.open .panah-icon { transform: rotate(0deg); }
        .mobile-dropdown-content { max-height: 0; overflow: hidden; background-color: #111827; position: absolute; top: 100%; left: 0; width: 100%; transition: max-height 0.3s ease-out; }
        .mobile-dropdown-content a { color: #94a3b8; padding: 14px 24px; display: block; text-decoration: none; font-size: 13px; border-bottom: 1px solid #1f2937; }
        .mobile-dropdown-content a.active_sub { background-color: #1f2937; color: #38bdf8; font-weight: 600; }
        .mobile-nav.open .mobile-dropdown-content { max-height: 250px; }

        /* KONTEN UTAMA */
        .main-content {
            flex: 1;
            padding: 40px;
            margin-left: 260px;
            display: flex;
            flex-direction: column;
            gap: 24px;
            max-width: 1400px;
            width: calc(100% - 260px);
            background: #f8fafc;
            color: #0f172a;
            min-height: 100vh;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 15px;
        }

        .header h1 { font-size: 26px; color: #0f172a; }

        @media screen and (max-width: 768px) {
            body { flex-direction: column; }
            .sidebar-kiri { display: none; }
            .mobile-nav { display: flex; }
            .main-content { margin-left: 0; padding: 20px; margin-top: 60px; width: 100%; }
            .header { flex-direction: column; align-items: flex-start; gap: 5px; }
            .header h1 { font-size: 20px; }
        }
    </style>

    @stack('styles')
</head>
<body>

    <!-- SIDEBAR KIRI -->
    <div class="sidebar-kiri">
        <div class="sidebar-top-section">
            <div class="sidebar-brand-container">
                <img src="{{ asset('img/gym.png') }}" alt="Logo Virgo Gym" class="sidebar-logo">
                <div class="sidebar-brand-text">
                    <h2>VIRGO GYM</h2>
                </div>
            </div>
            
            <div class="menu-links">
                <a href="{{ url('/') }}" class="{{ Request::is('/') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    Dashboard
                </a>
                
                @if(Auth::check() && Auth::user()->role == 'kasir')
                    <div class="menu-kategori">Manajemen Transaksi</div>
                    <a href="{{ route('harian.index') }}" class="{{ Request::is('harian*') ? 'active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                        Pengunjung Harian
                    </a>
                    <a href="{{ route('member.index') }}" class="{{ Request::is('member*') ? 'active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                        Data Member
                    </a>
                    <a href="{{ route('pelatih.index') }}" class="{{ Request::is('pelatih*') ? 'active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        Data Pelatih
                    </a>
                @endif

                @if(Auth::check() && Auth::user()->role == 'admin')
                    <div class="menu-kategori">Keuangan & Admin</div>
                    <a href="{{ route('harga.index') }}" class="{{ Request::is('harga*') ? 'active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                        Pengaturan Harga
                    </a>
                    <a href="{{ route('pelatih.index') }}" class="{{ Request::is('pelatih*') ? 'active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        Data Pelatih
                    </a>
                    <a href="{{ route('transaksi.index') }}" class="{{ Request::is('transaksi*') ? 'active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                        Catatan Transaksi
                    </a>
                @endif
            </div>
        </div>

        <!-- USER CARD & LOGOUT DI BAGIAN PALING BAWAH -->
        @auth
        <div class="sidebar-user-card">
            <div class="user-profile-info">
                <div class="user-avatar-badge">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div class="user-detail-text">
                    <h4>{{ Auth::user()->name }}</h4>
                    <span>{{ ucfirst(Auth::user()->role) }}</span>
                </div>
            </div>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="sidebar-logout-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    Logout
                </button>
            </form>
        </div>
        @endauth
    </div>

    <!-- NAVBAR MOBILE -->
    <div class="mobile-nav" id="mobileNavWrapper">
        <div class="mobile-brand">
            <img src="{{ asset('img/gym.png') }}" alt="Logo" class="mobile-logo">
            <h2>VIRGO GYM</h2>
        </div>

        <div class="mobile-menu-links">
            <a href="{{ url('/') }}" class="{{ Request::is('/') ? 'active' : '' }}">Dashboard</a>
            <button class="mobile-dropdown-btn {{ Request::is('harian*') || Request::is('member*') || Request::is('pelatih*') ? 'active' : '' }}" onclick="toggleMobileDropdown()">
                Menu <span class="panah-icon">▲</span>
            </button>
            <a href="{{ route('transaksi.index') }}" class="{{ Request::is('transaksi*') ? 'active' : '' }}">Transaksi</a>
        </div>

        <div class="mobile-dropdown-content">
            <a href="{{ route('harian.index') }}" class="{{ Request::is('harian*') ? 'active_sub' : '' }}">Harian</a>
            <a href="{{ route('member.index') }}" class="{{ Request::is('member*') ? 'active_sub' : '' }}">Member</a>
            <a href="{{ route('pelatih.index') }}" class="{{ Request::is('pelatih*') ? 'active_sub' : '' }}">Pelatih</a>
        </div>
    </div>

    <!-- KONTEN UTAMA -->
    <div class="main-content">
        <div class="header">
            <h1>@yield('page_title')</h1>
            
            @auth
            <div style="font-size: 13px; color: #64748b;">
                Sistem Informasi Manajemen Gym
            </div>
            @endauth
        </div>

        <div>
            @yield('konten')
        </div>
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