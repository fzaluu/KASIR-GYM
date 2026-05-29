<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catatan Transaksi - Kasir Gym</title>
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

        /* Desain Tabel Transaksi */
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

        /* Badge untuk Tipe Transaksi */
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        .badge-harian {
            background-color: #e0f2fe;
            color: #0369a1;
        }
        .badge-baru {
            background-color: #d1fae5;
            color: #065f46;
        }
        .badge-checkin {
            background-color: #fef3c7;
            color: #b45309;
        }

        /* ==========================================================================
           TAMBAHAN CODE UNTUK RESPONSIVE (TIDAK MERUBAH TAMPILAN DESKTOP ASLI)
           ========================================================================== */
        
        /* Menu Khusus Mobile (Default Tersembunyi di Desktop) */
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
                flex-direction: column; /* Mengubah alur dari menyamping ke bawah */
                height: auto;
                overflow: auto;
            }

            .sidebar {
                display: none; /* Sembunyikan sidebar desktop di layar kecil */
            }

            .mobile-nav {
                display: flex; /* Tampilkan menu ringkas di atas layar HP */
                position: sticky;
                top: 0;
                z-index: 999;
            }

            .main-content {
                padding: 15px; /* Perkecil padding agar ruang space lebih luas di HP */
                gap: 15px;
            }

            .header {
                flex-direction: column; /* Judul & Nama admin bertumpuk vertikal */
                align-items: flex-start;
                gap: 5px;
            }

            .header h1 {
                font-size: 18px; /* Perkecil ukuran font judul di HP */
            }

            /* Membungkus tabel agar bisa di-scroll ke samping jika layar terlalu sempit */
            .table-container {
                padding: 12px;
                overflow-x: auto; 
                -webkit-overflow-scrolling: touch;
            }

            table {
                min-width: 600px; /* Memaksa tabel menjaga kerapian struktur kolomnya */
            }

            th, td {
                padding: 10px 12px;
                font-size: 13px;
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
            <h1>Catatan Riwayat Transaksi Keuangan</h1>
            <span>Admin: <strong>FRAZA</strong></span>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal & Waktu</th>
                        <th>Tipe Transaksi</th>
                        <th>Nama Pelanggan / Member</th>
                        <th>Nominal Uang</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($semuaTransaksi as $index => $trx)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ date('d M Y - H:i:s', strtotime($trx->created_at)) }} WIB</td>
                            <td>
                                @if($trx->tipe_transaksi === 'Harian')
                                    <span class="badge badge-harian">Kunjungan Harian</span>
                                @elseif($trx->tipe_transaksi === 'Baru')
                                    <span class="badge badge-baru">Member Baru</span>
                                @else
                                    <span class="badge badge-checkin">Check-in Member</span>
                                @endif
                            </td>
                            <td><strong>{{ $trx->nama_pelanggan }}</strong></td>
                            <td><span style="color: #10b981; font-weight: 600;">Rp {{ number_format($trx->nominal, 0, ',', '.') }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #94a3b8; padding: 20px;">
                                Belum ada catatan transaksi keuangan yang masuk.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>