@extends('layouts.app')

@section('title', 'Dashboard Utama - Virgo Gym')
@section('page_title', 'Dashboard')

@push('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 25px;
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

    .action-area {
        display: flex;
        justify-content: flex-start;
        margin-bottom: 25px;
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

    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        background-color: #fff;
        box-sizing: border-box;
    }

    .radio-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-top: 6px;
    }

    .radio-grid label {
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

    @media screen and (max-width: 768px) {
        .stats-grid { grid-template-columns: 1fr; gap: 12px; }
        .table-container { padding: 15px; overflow-x: auto; }
        table { min-width: 550px; }
        .modal-box { width: 90%; padding: 20px; }
        .radio-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('konten')
    
    @if(session('sukses'))
        <div style="background-color: #d1fae5; color: #065f46; padding: 16px; border-radius: 8px; font-weight: 600; border: 1px solid #a7f3d0; margin-bottom: 15px;">
            {{ session('sukses') }}
        </div>
    @endif
    @if(session('gagal'))
        <div style="background-color: #fee2e2; color: #991b1b; padding: 16px; border-radius: 8px; font-weight: 600; border: 1px solid #fca5a5; margin-bottom: 15px;">
            {{ session('gagal') }}
        </div>
    @endif

    <div class="stats-grid">
        <div class="card">
            <h3>Total Pelatih</h3>
            <p>{{ $totalpelatih }} Orang</p>
        </div>
        <div class="card">
            <h3>Total Member Aktif</h3>
            <p>{{ $totalmemberaktif }} Orang</p>
        </div>
        <div class="card">
            <h3>Pemasukan Hari Ini</h3>
            <p>Rp {{ number_format($totalpemasukan, 0, ',', '.') }}</p>
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
                    <th>Nama Pelanggan</th>
                    <th>Tipe Transaksi</th>
                    <th>Nominal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logaktivitas as $item)
                <tr>
                    <td>{{ $item->created_at->format('H:i') }} WIB</td>
                    <td><strong>{{ $item->nama_pelanggan }}</strong></td>
                    <td>
                        @if($item->tipe_transaksi == 'Harian')
                             Kunjungan Harian
                        @elseif($item->tipe_transaksi == 'Checkin')
                            Check-in Member Lama    
                        @elseif($item->tipe_transaksi == 'Perpanjang')
                            Perpanjang Member    
                        @elseif($item->tipe_transaksi == 'Baru')
                            Member Baru (Registrasi)
                        @elseif($item->tipe_transaksi == 'Sewa PT')
                            Sewa Jasa PT
                        @else
                            {{ $item->tipe_transaksi }}
                        @endif
                    </td>
                    <td>
                        @if($item->nominal == 0)
                            <span style="color: #64748b; font-weight: 600;">Gratis</span>
                        @else
                            <span style="color: #10b981; font-weight: 600;">Rp {{ number_format($item->nominal, 0, ',', '.') }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @php
        // Ambil array ID member yang sudah melakukan check-in hari ini dari data logaktivitas dashboard
        $idSudahCheckin = $logaktivitas->where('tipe_transaksi', 'Checkin')->pluck('member_id')->toArray();
    @endphp

    <div class="modal-overlay" id="modalTransaksi">
        <div class="modal-box">
            <div class="modal-header">
                <h2>Form Transaksi & Check-in</h2>
                <button class="btn-close" id="btnTutupModal">&times;</button>
            </div>
            
           <form action="{{ route('transaksi.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Pilihan Aksi:</label>
                    <div class="radio-grid">
                        <label><input type="radio" name="tipe_kunjungan" value="harian" checked> Kunjungan Harian</label>
                        <label><input type="radio" name="tipe_kunjungan" value="checkin"> Check-in Member Tetap</label>
                        <label><input type="radio" name="tipe_kunjungan" value="perpanjang"> Perpanjang Member Gym</label>
                    </div>
                </div>

                <div class="form-group" id="inputNama">
                    <label>Nama Pengunjung Harian:</label>
                    <input type="text" name="nama" class="form-control" placeholder="Masukkan nama pengunjung harian...">
                </div>

                <div class="form-group" id="selectMemberCheckin" style="display: none;">
                    <label>Pilih Nama Member (Hanya Member Aktif):</label>
                    <select name="member_id" id="memberIdCheckin" class="form-control">
                        <option value="">-- Pilih Member Aktif --</option>
                        @foreach($daftarMember as $member)
                            @if(!in_array($member->id, $idSudahCheckin))
                                <option value="{{ $member->id }}">{{ $member->nama_member }} ({{ $member->nomor_telepon }})</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="form-group" id="selectMemberPerpanjang" style="display: none;">
                    <label>Pilih Nama Member:</label>
                    <select name="member_id" id="memberIdPerpanjang" class="form-control" disabled>
                        <option value="">-- Pilih Member --</option>
                        @foreach($daftarMemberSemua as $member)
                            <option value="{{ $member->id }}">{{ $member->nama_member }} ({{ $member->nomor_telepon }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" id="inputNominal">
                    <label>Total Bayar (Rp):</label>
                    <input type="number" name="nominal" id="nominalInput" value="8000" class="form-control" required>
                </div>

                <button type="submit" class="btn-simpan">SIMPAN TRANSAKSI KASIR</button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const modal = document.getElementById('modalTransaksi');
    const btnBuka = document.getElementById('btnBukaModal');
    const btnTutup = document.getElementById('btnTutupModal');
    
    const radioTipe = document.querySelectorAll('input[name="tipe_kunjungan"]');
    const inputNama = document.getElementById('inputNama');
    const selectMemberCheckin = document.getElementById('selectMemberCheckin');
    const selectMemberPerpanjang = document.getElementById('selectMemberPerpanjang');
    const memberIdCheckin = document.getElementById('memberIdCheckin');
    const memberIdPerpanjang = document.getElementById('memberIdPerpanjang');
    const inputNominal = document.getElementById('inputNominal');
    const fieldNominal = document.getElementById('nominalInput');

    btnBuka.addEventListener('click', () => modal.classList.add('show'));
    btnTutup.addEventListener('click', () => modal.classList.remove('show'));

    radioTipe.forEach(radio => {
        radio.addEventListener('change', (e) => {
            inputNama.style.display = 'none';
            selectMemberCheckin.style.display = 'none';
            selectMemberPerpanjang.style.display = 'none';
            memberIdCheckin.disabled = true;
            memberIdPerpanjang.disabled = true;
            inputNominal.style.display = 'block';

            if (e.target.value === 'harian') {
                inputNama.style.display = 'block';
                fieldNominal.value = '8000';
            } else if (e.target.value === 'checkin') {
                selectMemberCheckin.style.display = 'block';
                memberIdCheckin.disabled = false;
                inputNominal.style.display = 'none';
                fieldNominal.value = '0';
            } else if (e.target.value === 'perpanjang') {
                selectMemberPerpanjang.style.display = 'block';
                memberIdPerpanjang.disabled = false;
                fieldNominal.value = '100000';
            }
        });
    });
</script>
@endpush