<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kasir Gym</title>
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
            height: 100vh;
            overflow: hidden;
        }

        /* Navigasi Sidebar Kiri */
        .sidebar {
            width: 260px;
            background-color: #1e293b;
            color: #fff;
            padding: 24px;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 20px;
            color: #38bdf8;
            letter-spacing: 1px;
        }

        .sidebar a {
            color: #94a3b8;
            text-decoration: none;
            padding: 12px 16px;
            margin-bottom: 8px;
            border-radius: 8px;
            display: block;
            transition: all 0.2s;
        }

        .sidebar a.active, .sidebar a:hover {
            background-color: #334155;
            color: #fff;
        }

        /* Konten Utama Kanan */
        .main-content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 24px;
            color: #0f172a;
        }

        /* Grid untuk Kotak Informasi (Atas) */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .card {
            background-color: #fff;
            padding: 24px;
            border-radius: 10px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
        }

        .card h3 {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card p {
            font-size: 28px;
            font-weight: 700;
            color: #0f172a;
        }

        /* Area Tombol Aksi */
        .action-area {
            display: flex;
            justify-content: flex-start;
        }

        .btn-aksi {
            background-color: #38bdf8;
            color: #fff;
            border: none;
            padding: 14px 24px;
            border-radius: 8px;
            font-size: 15px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.2s;
            box-shadow: 0 4px 6px -1px rgba(56, 189, 248, 0.2);
        }

        .btn-aksi:hover {
            background-color: #0ea5e9;
        }

        /* Desain Tabel Manual */
        .table-container {
            background-color: #fff;
            padding: 24px;
            border-radius: 10px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
        }

        .table-container h3 {
            margin-bottom: 16px;
            color: #0f172a;
            font-size: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th, td {
            padding: 14px 16px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }

        th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
        }

        tr:hover td {
            background-color: #f8fafc;
        }

        /* CSS MODAL MANUAL */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(15, 23, 42, 0.6);
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            pointer-events: none;
            transition: all 0.25s ease;
            z-index: 999;
        }

        .modal-overlay.show {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-box {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            width: 480px;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
            transform: scale(0.9);
            transition: all 0.25s ease;
        }

        .modal-overlay.show .modal-box {
            transform: scale(1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 12px;
        }

        .modal-header h2 {
            font-size: 18px;
            color: #0f172a;
        }

        .btn-close {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #94a3b8;
            line-height: 1;
        }

        .btn-close:hover {
            color: #64748b;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 6px;
            color: #334155;
        }

        .form-group input[type="text"],
        .form-group input[type="number"] {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
        }

        .form-group input:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.1);
        }

        .radio-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 6px;
        }

        .radio-group label {
            font-weight: 400;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #475569;
        }

        .btn-simpan {
            width: 100%;
            background-color: #10b981;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
            font-size: 15px;
        }

        .btn-simpan:hover {
            background-color: #059669;
        }

        /* ==========================================================================
           TAMBAHAN CODE UNTUK RESPONSIVE (TIDAK MERUBAH TAMPILAN DESKTOP ASLI)
           ========================================================================== */
        
        /* Menu Utama Khusus Mobile (Default Tersembunyi di Desktop) */
        .mobile-nav {
            display: none;
            background-color: #1e293b;
            padding: 10px 15px;
            justify-content: space-between;
            align-items: center;
        }
        .mobile-nav h2 {
            font-size: 16px;
            color: #38bdf8;
        }
        .mobile-menu-links {
            display: flex;
            gap: 8px;
        }
        .mobile-menu-links a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 12px;
            padding: 6px 10px;
            border-radius: 6px;
        }
        .mobile-menu-links a.active {
            background-color: #334155;
            color: #fff;
        }

        /* Media Query untuk Layar Tablet & HP (Maksimal Lebar 768px) */
        @media screen and (max-width: 768px) {
            body {
                flex-direction: column;
                height: auto;
                overflow: auto;
            }

            .sidebar {
                display: none; /* Sembunyikan sidebar desktop */
            }

            .mobile-nav {
                display: flex; /* Tampilkan navigasi atas khusus HP */
                position: sticky;
                top: 0;
                z-index: 999;
            }

            .main-content {
                padding: 15px;
                gap: 15px;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }

            .header h1 {
                font-size: 20px;
            }

            /* Ubah susunan 2 Kolom Card menjadi 1 Kolom berderet ke bawah */
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .card {
                padding: 16px;
            }

            .card p {
                font-size: 22px;
            }

            .btn-aksi {
                width: 100%; /* Tombol transaksi baru memenuhi lebar layar HP */
                text-align: center;
                padding: 12px;
                font-size: 14px;
            }

            /* Membungkus tabel aktivitas agar bisa digeser (Scrollable) */
            .table-container {
                padding: 15px;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            table {
                min-width: 550px; /* Menjaga struktur kolom log tetap ideal */
            }

            th, td {
                padding: 10px 12px;
                font-size: 13px;
            }

            /* Penyesuaian Ukuran Box Modal di HP */
            .modal-box {
                width: 90%; /* Mengikuti lebar layar HP dengan margin kanan-kiri 5% */
                padding: 20px;
            }
        }
    </style>
</head>
<body>

    <div class="mobile-nav">
        <h2>GYM KASIR</h2>
        <div class="mobile-menu-links">
            <a href="{{ url('/') }}" class="{{ Request::is('/') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('member.index') }}" class="{{ Route::is('member.*') || Request::is('member*') ? 'active' : '' }}">Member</a>
            <a href="{{ route('transaksi.index') }}" class="{{ Route::is('transaksi.*') || Request::is('transaksi*') ? 'active' : '' }}">Transaksi</a>
        </div>
    </div>

    <div class="sidebar">
        <h2>GYM KASIR</h2>
        <a href="{{ url('/') }}" class="{{ Request::is('/') ? 'active' : '' }}">Dashboard</a>
        <a href="{{ route('member.index') }}" class="{{ Route::is('member.*') || Request::is('member*') ? 'active' : '' }}">Data Member</a>
        <a href="{{ route('transaksi.index') }}" class="{{ Route::is('transaksi.*') || Request::is('transaksi*') ? 'active' : '' }}">Catatan Transaksi</a>
    </div>

    <div class="main-content">
        
        @if(session('sukses'))
            <div style="background-color: #d1fae5; color: #065f46; padding: 166px; padding: 16px; border-radius: 8px; font-weight: 600; border: 1px solid #a7f3d0;">
                {{ session('sukses') }}
            </div>
        @endif
        @if(session('gagal'))
            <div style="background-color: #fee2e2; color: #991b1b; padding: 16px; border-radius: 8px; font-weight: 600; border: 1px solid #fca5a5; margin-bottom: 15px;">
                {{ session('gagal') }}
            </div>
        @endif
        
        <div class="header">
            <h1>Dashboard</h1>
            <span>Admin: <strong>FRAZA</strong></span>
        </div>

        <div class="stats-grid">
            <div class="card">
                <h3>Pemasukan Hari Ini</h3>
                <p>Rp{{ number_format($totalpemasukan, 0, ',','.') }}</p>
            </div>
            <div class="card">
                <h3>Total Member Aktif</h3>
                <p>{{ $totalmemberaktif }} Orang</p>
            </div>
        </div>

        <div class="action-area">
            <button class="btn-aksi" id="btnBukaModal">[+] TRANSAKSI BARU / CHECK-IN</button>
        </div>

        <div class="table-container">
            <h3>Log Aktivitas Hari Ini</h3>
            <table>
                <thead>
                    <tr>
                        <th>Jam</th>
                        <th>Jenis Kunjungan</th>
                        <th>Keterangan</th>
                        <th>Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logaktivitas as $item)
                    <tr>
                        <td>{{ $item->created_at->format('H:i') }}</td>
                        <td>{{ $item->tipe_transaksi }} ({{ $item->nama_pelanggan }})</td>
                        <td>
                            @if($item->tipe_transaksi == 'Harian')
                        Tiket Masuk Harian
                    @elseif($item->tipe_transaksi == 'Checkin')
                        Check-in Member Lama    
                    @elseif($item->tipe_transaksi == 'Perpanjang')
                        Perpanjang Member    
                    @elseif($item->tipe_transaksi == 'Baru')
                        Pendaftaran Member Baru
                    @else
                        Tipe Tidak Diketahui
                    @endif
                        </td>
                        <td>
                            @if($item->nominal == 0)
                                
                            @else
                                Rp {{ number_format($item->nominal, 0, ',','.') }}
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

   <div class="modal-overlay" id="modalTransaksi">
    <div class="modal-box">
        <div class="modal-header">
            <h2>Form Transaksi & Check-in</h2>
            <button class="btn-close" id="btnTutupModal">&times;</button>
        </div>
        
        <form action="{{ route('transaksi.simpan') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Pilihan Aksi:</label>
                <div class="radio-group">
                    <label><input type="radio" name="tipe_kunjungan" value="harian" checked> Harian / Non-Member</label>
                    <label><input type="radio" name="tipe_kunjungan" value="checkin"> Check-in Member Lama</label>
                    <label><input type="radio" name="tipe_kunjungan" value="baru"> Member Baru</label>
                    <label><input type="radio" name="tipe_kunjungan" value="perpanjang"> Perpanjang Member (Renew)</label>
                </div>
            </div>

            <div class="form-group" id="inputNama">
                <label>Nama Pelanggan / Member:</label>
                <input type="text" name="nama" placeholder="Masukkan nama..." required>
            </div>

            <div class="form-group" id="inputTelepon" style="display: none;">
                <label>No. Telepon:</label>
                <input type="text" name="nomor_telepon" placeholder="Masukkan nomor telepon...">
            </div>

            <div class="form-group" id="inputNominal">
                <label>Total Bayar (Rp):</label>
                <input type="number" name="nominal" id="nominalInput" value="10000" required>
            </div>

            <button type="submit" class="btn-simpan">SIMPAN TRANSAKSI</button>
        </form>
    </div>
</div>
        </div>
    </div>

    <script>
    // ==========================================
    // 1. PENGENDALI MODAL TRANSAKSI (DIPERTAHANKAN & DISESUAIKAN)
    // ==========================================
    const modal = document.getElementById('modalTransaksi');
    const btnBuka = document.getElementById('btnBukaModal');
    const btnTutup = document.getElementById('btnTutupModal');
    
    const radioTipe = document.querySelectorAll('input[name="tipe_kunjungan"]');
    const inputTelepon = document.getElementById('inputTelepon');
    const inputNama = document.getElementById('inputNama');
    const inputNominal = document.getElementById('inputNominal');
    const fieldNominal = inputNominal.querySelector('input');

    // Membuka dan menutup modal utama dashboard
    btnBuka.addEventListener('click', () => modal.classList.add('show'));
    btnTutup.addEventListener('click', () => modal.classList.remove('show'));

    // ==========================================
    // 2. LOGIKA KONDISI INPUT FORM (ADA TAMBAHAN 'PERPANJANG')
    // ==========================================
    radioTipe.forEach(radio => {
        radio.addEventListener('change', (e) => {
            if (e.target.value === 'harian') {
                inputNama.style.display = 'block';
                inputTelepon.style.display = 'none';
                inputNominal.style.display = 'block';
                fieldNominal.value = '10000';
            } else if (e.target.value === 'checkin') {
                inputNama.style.display = 'block';
                inputTelepon.style.display = 'none';
                // Menyembunyikan input nominal karena check-in bernilai Rp 0
                inputNominal.style.display = 'none'; 
                fieldNominal.value = '0';
            } else if (e.target.value === 'baru') {
                inputNama.style.display = 'block';
                // Memunculkan input telepon untuk meregistrasi data nomor HP member baru
                inputTelepon.style.display = 'block'; 
                inputNominal.style.display = 'block';
                fieldNominal.value = '100000';
            } 
            // 🌟 TAMBAHAN BARU: Logika Tampilan Untuk Perpanjang Member
            else if (e.target.value === 'perpanjang') {
                inputNama.style.display = 'block';
                inputTelepon.style.display = 'none'; // Sembunyikan telepon karena datanya sudah ada di database member lama
                inputNominal.style.display = 'block'; // Munculkan nominal bayar perpanjangan bulanan
                fieldNominal.value = '100000'; // Nominal default perpanjang bulanan
            }
        });
    });

    // ==========================================
    // 3. SCRIPT TAMBAHAN: PENGENDALI MENU MOBILE HP
    // ==========================================
    const menuHP = document.getElementById('menuHP');
    const sidebar = document.querySelector('.sidebar');
    const backdropHP = document.getElementById('backdropHP');

    if (menuHP) {
        menuHP.addEventListener('click', () => {
            sidebar.classList.add('show');
            backdropHP.classList.add('show');
        });
    }

    if (backdropHP) {
        backdropHP.addEventListener('click', () => {
            sidebar.classList.remove('show');
            backdropHP.classList.remove('show');
        });
    }
</script>
</body>
</html>