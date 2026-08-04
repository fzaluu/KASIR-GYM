@extends('layouts.app')

@section('title', 'Transaksi')
@section('page_title', 'Catatan Transaksi')

@push('styles')
<style>
    .table-container {
        background-color: #fff;
        padding: 24px;
        border-radius: 10px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
    }

    /* Style untuk box filter pencarian */
    .filter-container {
        background-color: #f8fafc;
        padding: 16px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        margin-bottom: 20px;
    }

    /* Mengatur form agar elemennya sejajar rapi kesamping */
    .filter-form {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: flex-end;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
        flex: 1;
        min-width: 150px;
    }

    .form-group label {
        font-size: 12px;
        color: #64748b;
        font-weight: 500;
    }

    .form-control {
        padding: 8px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 14px;
        background-color: #fff;
        color: #334155;
        outline: none;
    }

    .form-control:focus {
        border-color: #3b82f6;
    }

    .btn-filter {
        padding: 8px 16px;
        background-color: #3b82f6;
        color: #fff;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
    }

    .btn-excel {
        padding: 8px 16px;
        background-color: #10b981;
        color: #fff;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .btn-excel:hover {
        background-color: #059669;
    }

    .btn-reset {
        padding: 8px 16px;
        background-color: #64748b;
        color: #fff;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        text-align: center;
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

    /* Badge warna status tipe transaksi */
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
    .badge-perpanjang {
        background-color: #e0e7ff;
        color: #3730a3;
    }
    .badge-checkin {
        background-color: #fef3c7;
        color: #b45309;
    }
    .badge-sewa-pt {
        background-color: #fae8ff;
        color: #86198f;
    }

    @media screen and (max-width: 768px) {
        .table-container {
            padding: 12px;
            overflow-x: auto; 
            -webkit-overflow-scrolling: touch;
        }

        .filter-form {
            flex-direction: column;
            align-items: stretch;
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

@section('konten')
    <div class="filter-container">
        <form action="{{ route('transaksi.index') }}" method="GET" id="formFilterTransaksi" class="filter-form">
            <div class="form-group">
                <label>Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" id="filterTanggalMulai" class="form-control" value="{{ request('tanggal_mulai', \Carbon\Carbon::today()->toDateString()) }}">
            </div>

            <div class="form-group">
                <label>Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" id="filterTanggalSelesai" class="form-control" value="{{ request('tanggal_selesai', \Carbon\Carbon::today()->toDateString()) }}">
            </div>
            
            <div class="form-group">
                <label>Tipe Transaksi</label>
                <select name="tipe_transaksi" id="filterTipeTransaksi" class="form-control">
                    <option value="Semua Tipe" {{ request('tipe_transaksi') == 'Semua Tipe' ? 'selected' : '' }}>Semua Tipe</option>
                    <option value="Harian" {{ request('tipe_transaksi') == 'Harian' ? 'selected' : '' }}>Kunjungan Harian</option>
                    <option value="Baru" {{ request('tipe_transaksi') == 'Baru' ? 'selected' : '' }}>Member Baru</option>
                    <option value="Perpanjang" {{ request('tipe_transaksi') == 'Perpanjang' ? 'selected' : '' }}>Perpanjang Member</option>
                    <option value="Checkin" {{ request('tipe_transaksi') == 'Checkin' ? 'selected' : '' }}>Check-in Member</option>
                    <option value="Sewa PT" {{ request('tipe_transaksi') == 'Sewa PT' ? 'selected' : '' }}>Sewa Jasa PT (Pelatih)</option>
                </select>
            </div>
            
            <button type="submit" class="btn-filter">Cari Filter</button>
            
            <button type="button" class="btn-excel" onclick="prosesCetakExcel()">Cetak Excel</button>
            
            <a href="{{ route('transaksi.index') }}" class="btn-reset">Reset</a>
        </form>
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
                            @elseif($trx->tipe_transaksi === 'Perpanjang')
                                <span class="badge badge-perpanjang">Perpanjang Member</span>
                            @elseif($trx->tipe_transaksi === 'Sewa PT')
                                <span class="badge badge-sewa-pt">Sewa Jasa PT</span>
                            @else
                                <span class="badge badge-checkin">Check-in Member</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $trx->nama_pelanggan }}</strong>
                            @if($trx->tipe_transaksi === 'Sewa PT' && $trx->pelatih_id)
                                <br><small style="color: #64748b;">Pelatih: {{ $trx->pelatih->nama_pelatih ?? 'Pelatih' }}</small>
                            @endif
                        </td>
                        <td>
                            @if($trx->nominal > 0)
                                <span style="color: #10b981; font-weight: 600;">Rp {{ number_format($trx->nominal, 0, ',', '.') }}</span>
                            @else
                                <span style="color: #94a3b8; font-weight: 500;">Rp 0 (Log)</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #94a3b8; padding: 20px;">
                            Belum ada catatan transaksi keuangan yang masuk pada filter ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
<script>
    function prosesCetakExcel() {
        // Ambil elemen filter input
        const tglMulai = document.getElementById('filterTanggalMulai').value;
        const tglSelesai = document.getElementById('filterTanggalSelesai').value;
        const selectTipe = document.getElementById('filterTipeTransaksi');
        const tipeText = selectTipe.options[selectTipe.selectedIndex].text;
        const tipeValue = selectTipe.value;

        // Racik pesan konfirmasi validasi secara dinamis sesuai pilihan admin
        let pesanValidasi = "";
        if (tipeValue === "Semua Tipe") {
            pesanValidasi = `Print semua tipe transaksi?\nPeriode: ${tglMulai} s.d ${tglSelesai}\n\nKlik OK untuk konfirmasi.`;
        } else {
            pesanValidasi = `Print laporan ${tipeText} saja?\nPeriode: ${tglMulai} s.d ${tglSelesai}\n\nKlik OK untuk konfirmasi.`;
        }

        // Tampilkan konfirmasi pop-up interaktif
        if (confirm(pesanValidasi)) {
            // Jika OK, alihkan window browser ke route export excel murni bawaan controller kita
            const urlExport = `{{ route('transaksi.export') }}?tanggal_mulai=${tglMulai}&tanggal_selesai=${tglSelesai}&tipe_transaksi=${encodeURIComponent(tipeValue)}`;
            window.location.href = urlExport;
        }
        // Jika Cancel, otomatis keluar dan proses dibatalkan aman.
    }
</script>
@endpush