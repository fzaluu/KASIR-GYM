<!-- 1. Sambungkan halaman ini ke file induk layouts/app.blade.php -->
@extends('layouts.app')

<!-- 2. Set Judul Tab Browser dan Judul Halaman Atas -->
@section('title', 'Catatan Transaksi - Kasir Gym')
@section('page_title', 'Catatan Riwayat Transaksi Keuangan')

<!-- 3. TITIPKAN CSS KHUSUS HALAMAN TRANSAKSI KE FILE INDUK -->
@push('styles')
<style>
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

    /* CSS Responsive khusus halaman transaksi di HP */
    @media screen and (max-width: 768px) {
        .table-container {
            padding: 12px;
            overflow-x: auto; 
            -webkit-overflow-scrolling: touch;
        }

        table {
            min-width: 600px;
        }

        th, td {
            padding: 10px 12px;
            font-size: 13px;
        }
    }
</style>
@endpush

<!-- 4. MASUKKAN ISI KONTEN UTAMA RIWAYAT TRANSAKSI -->
@section('konten')
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
@endsection