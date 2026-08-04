<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
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
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 100;
        }

        .sidebar-top-section {
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 5px 0 12px 0;
            margin-bottom: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .sidebar-logo {
            width: 100px;
            height: 100px;
            object-fit: contain;
            margin-bottom: 4px;
            filter: drop-shadow(0px 0px 12px rgba(56, 189, 248, 0.5)); 
        }

        .sidebar-brand-text h2 {
            font-size: 19px;
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

        .sidebar-user-card {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            padding: 14px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 30px;
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

        /* Page Loader */
        #page-loader {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(6px);
            z-index: 9999;
            display: flex; flex-direction: column; justify-content: center; align-items: center;
            color: #ffffff; opacity: 0; visibility: hidden;
            transition: opacity 0.25s ease, visibility 0.25s ease;
        }
        #page-loader.show { opacity: 1; visibility: visible; }

        .spinner-ring {
            width: 3rem; height: 3rem; border: 3px solid rgba(255,255,255,0.2);
            border-top-color: #fff; border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* === MODAL LOGOUT CONFIRMATION === */
        .logout-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 10000;
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.25s ease-in-out;
        }

        .logout-modal-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .logout-modal-box {
            background: #1e293b;
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 16px;
            width: 100%;
            max-width: 380px;
            text-align: center;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
            transform: scale(0.9);
            transition: transform 0.25s ease-in-out;
            color: #f8fafc;
        }

        .logout-modal-overlay.show .logout-modal-box {
            transform: scale(1);
        }

        .logout-modal-icon {
            width: 50px;
            height: 50px;
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }

        .logout-modal-box h3 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 8px;
            color: #fff;
        }

        .logout-modal-box p {
            font-size: 13px;
            color: #94a3b8;
            margin-bottom: 24px;
            line-height: 1.5;
        }

        .logout-modal-actions {
            display: flex;
            gap: 12px;
        }

        .logout-btn-batal, .logout-btn-ya {
            flex: 1;
            padding: 10px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }

        .logout-btn-batal {
            background: rgba(255, 255, 255, 0.08);
            color: #cbd5e1;
        }

        .logout-btn-batal:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
        }

        .logout-btn-ya {
            background: #ef4444;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .logout-btn-ya:hover {
            background: #dc2626;
        }

        .logout-btn-ya.loading {
            pointer-events: none;
            opacity: 0.85;
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
                
                @if(Auth::check() && Auth::user()->isKasir())
                    <div class="menu-kategori"> Transaksi</div>
                    <a href="{{ route('harian.index') }}" class="{{ Request::is('harian*') ? 'active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                        Harian
                    </a>
                    <a href="{{ route('member.index') }}" class="{{ Request::is('member*') ? 'active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                        Member
                    </a>
                    <a href="{{ route('pelatih.index') }}" class="{{ Request::is('pelatih*') ? 'active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        Pelatih
                    </a>
                @endif

                @if(Auth::check() && Auth::user()->isAdmin())
                    <div class="menu-kategori">Keuangan & Admin</div>
                    <a href="{{ route('harga.index') }}" class="{{ Request::is('harga*') ? 'active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                        Pengaturan Harga
                    </a>
                    <a href="{{ route('pelatih.index') }}" class="{{ Request::is('pelatih*') ? 'active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        Pelatih
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
                    <span>{{ ucfirst(Auth::user()->role?->slug ?? 'guest') }}</span>
                </div>
            </div>

            <form action="{{ route('logout') }}" method="POST" id="logoutForm">
                @csrf
                <button type="button" class="sidebar-logout-btn" onclick="bukaModalLogout()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    Logout
                </button>
            </form>
        </div>
        @endauth
    </div>

    <!-- Global Loading Overlay -->
    <div id="page-loader">
        <div class="spinner-ring mb-3"></div>
        <div class="fw-medium text-light small tracking-wide">Yeah buddy! Lightweight, baby!</div>
    </div>

    <!-- MODAL KONFIRMASI LOGOUT -->
    <div class="logout-modal-overlay" id="logoutModal">
        <div class="logout-modal-box">
            <div class="logout-modal-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
            </div>
            <h3>Konfirmasi Logout</h3>
            <p>Apakah kamu yakin ingin keluar dari sistem Virgo Gym?</p>
            <div class="logout-modal-actions">
                <button type="button" class="logout-btn-batal" onclick="tutupModalLogout()">Batal</button>
                <button type="button" class="logout-btn-ya" id="btnKonfirmasiLogout" onclick="eksekusiLogout()">Ya, Keluar</button>
            </div>
        </div>
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

        // Fungsi Modal Logout
        function bukaModalLogout() {
            document.getElementById('logoutModal').classList.add('show');
        }

        function tutupModalLogout() {
            document.getElementById('logoutModal').classList.remove('show');
        }

        function eksekusiLogout() {
            const btnYa = document.getElementById('btnKonfirmasiLogout');
            btnYa.classList.add('loading');
            btnYa.innerHTML = `
                <svg width="18" height="18" viewBox="0 0 50 50" style="vertical-align: middle; margin-right: 6px;">
                    <circle cx="25" cy="25" r="20" fill="none" stroke="white" stroke-width="5" stroke-linecap="round" stroke-dasharray="31.4 31.4">
                        <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="0.8s" values="0 25 25;360 25 25"/>
                    </circle>
                </svg>
                Keluar...
            `;

            const loader = document.getElementById('page-loader');
            if(loader) loader.classList.add('show');
            
            document.getElementById('logoutForm').submit();
        }

        // Tutup modal jika user klik di luar kotak modal
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('logoutModal');
            if (event.target === modal) {
                tutupModalLogout();
            }
        });

        // Page Loader on Link & Form Submit
        document.addEventListener("DOMContentLoaded", function() {
            const loader = document.getElementById('page-loader');
            
            document.querySelectorAll('.sidebar-kiri a').forEach(link => {
                link.addEventListener('click', function(e) {
                    let href = this.getAttribute('href');
                    if (href && href !== '#' && !href.startsWith('javascript') && !this.hasAttribute('data-bs-toggle')) {
                        loader.classList.add('show');
                    }
                });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>