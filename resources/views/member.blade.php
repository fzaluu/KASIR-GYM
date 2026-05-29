<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Member - Kasir Gym</title>
    <style>
        /* CSS RESET & BASE */
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

        /* Link Member kita set aktif di halaman ini */
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

        /* Area Tombol Aksi */
        .action-area {
            display: flex;
            justify-content: flex-start;
        }

        .btn-aksi {
            background-color: #10b981; /* Warna hijau untuk tambah member */
            color: #fff;
            border: none;
            padding: 14px 24px;
            border-radius: 8px;
            font-size: 15px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.2s;
            box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2);
        }

        .btn-aksi:hover {
            background-color: #059669;
        }

        /* Desain Tabel Manual */
        .table-container {
            background-color: #fff;
            padding: 24px;
            border-radius: 10px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
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

        /* Badge Status Sisa Hari */
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-aktif {
            background-color: #d1fae5;
            color: #065f46;
        }
        .badge-habis {
            background-color: #fee2e2;
            color: #991b1b;
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
        .form-group input[type="date"] {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
        }

        .form-group input:focus {
            border-color: #38bdf8;
        }

        .btn-simpan {
            width: 100%;
            background-color: #38bdf8;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
            font-size: 15px;
        }

        /* ==========================================================================
           TAMBAHAN CODE UNTUK RESPONSIVE (TIDAK MERUBAH TAMPILAN DESKTOP ASLI)
           ========================================================================== */

        /* Menu Khusus Tampilan Mobile (Default: Tersembunyi di Desktop) */
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

        /* Media Query untuk Layar Gadget / HP / Tablet (Lebar Maksimal 768px) */
        @media screen and (max-width: 768px) {
            body {
                flex-direction: column;
                height: auto;
                overflow: auto;
            }

            .sidebar {
                display: none; /* Sembunyikan sidebar bawaan desktop */
            }

            .mobile-nav {
                display: flex; /* Aktifkan menu bar atas di mobile */
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

            .btn-aksi {
                width: 100%; /* Tombol registrasi memanjang penuh di layar HP */
                text-align: center;
                padding: 12px;
                font-size: 14px;
            }

            /* Membungkus data tabel member agar bisa digeser horizontal */
            .table-container {
                padding: 12px;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            table {
                min-width: 650px; /* Menjaga struktur kolom data member agar tidak gepeng */
            }

            th, td {
                padding: 10px 12px;
                font-size: 13px;
            }

            /* Fleksibilitas kotak modal input di HP */
            .modal-box {
                width: 90%;
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
        
        <div class="header">
            @if(session('sukses'))
                <div style="background-color: #d1fae5; color: #065f46; padding: 16px; border-radius: 8px; font-weight: 600; border: 1px solid #a7f3d0; margin-bottom: 15px; width: 100%;">
                    {{ session('sukses') }}
                </div>
            @endif
            <h1>Data Member Terdaftar</h1>
            <span>Admin: <strong>FRAZA</strong></span>
        </div>

        <div class="action-area">
            <button class="btn-aksi" id="btnBukaModal">[+] DAFTARKAN MEMBER BARU VIA FORM</button>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Member</th>
                        <th>No. Telepon</th>
                        <th>Masa Aktif Berakhir</th>
                        <th>Status / Sisa Hari</th>
                        <th>Total Gym (Check-in)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($daftarMember as $index => $member)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $member->nama_member }}</strong></td>
                            <td>{{ $member->nomor_telepon }}</td>
                            <td>{{ date('d M Y', strtotime($member->tanggal_kadaluarsa)) }}</td>
                            <td>
                                @if($member->sisa_hari === 'HABIS')
                                    <span class="badge badge-habis">Masa Aktif Habis</span>
                                @else
                                    <span class="badge badge-aktif">{{ $member->sisa_hari }}</span>
                                @endif
                            </td>
                            <td>{{ $member->absensi->count() }} Kali Datang</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: #94a3b8; padding: 20px;">
                                Belum ada data member terdaftar di database.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal-overlay" id="modalMember">
        <div class="modal-box">
            <div class="modal-header">
                <h2>Form Registrasi Member Baru</h2>
                <button class="btn-close" id="btnTutupModal">&times;</button>
            </div>
            
            <form action="{{ route('transaksi.simpan') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label>Nama Lengkap:</label>
                    <input type="text" name="nama" placeholder="Nama lengkap calon member..." required>
                </div>

                <div class="form-group">
                    <label>No. Telepon (WhatsApp):</label>
                    <input type="text" name="nomor_telepon" placeholder="Contoh: 0812345xxxx" required>
                </div>

                <input type="hidden" name="tipe_kunjungan" value="baru">
                <input type="hidden" name="nominal" value="100000">

                <button type="submit" class="btn-simpan">DAFTARKAN MEMBER</button>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('modalMember');
        const btnBuka = document.getElementById('btnBukaModal');
        const btnTutup = document.getElementById('btnTutupModal');

        btnBuka.addEventListener('click', () => modal.classList.add('show'));
        btnTutup.addEventListener('click', () => modal.classList.remove('show'));
    </script>
</body>
</html>