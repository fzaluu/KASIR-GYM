@extends('layouts.app')

@section('title', 'Dashboard Utama - Virgo Gym')
@section('page_title', 'Dashboard')

@push('styles')
<style>
    /* === SKELETON LOADING ANIMATION === */
    .skeleton {
        background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
        background-size: 200% 100%;
        animation: skeleton-loading 1.5s infinite;
        border-radius: 6px;
    }

    @keyframes skeleton-loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    .skeleton-text {
        height: 16px;
        width: 80%;
        margin-bottom: 8px;
    }

    .skeleton-title {
        height: 28px;
        width: 40%;
        margin-bottom: 12px;
    }

    .skeleton-box {
        height: 80px;
        width: 100%;
        border-radius: 10px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 25px;
    }

    .rekap-container {
        display: flex;
        gap: 20px;
        margin-bottom: 5px;
        flex-wrap: wrap;
    }

    .kartu-rekap {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        flex: 1;
        min-width: 220px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    }

    .kartu-rekap h3 {
        font-size: 14px;
        color: #64748b;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .kartu-rekap .angka {
        font-size: 28px;
        font-weight: 700;
        color: #0f172a;
    }

    .kartu-rekap .trend-naik {
        font-size: 12px;
        color: #10b981;
        font-weight: 600;
        margin-top: 5px;
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
        color: #0f172a;
    }

    th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 600;
    }

    tr:hover td {
        background-color: #f8fafc;
    }

    /* === STYLING PAGINATION MODERN & RAPI === */
    .pagination-container {
        margin-top: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        border-top: 1px solid #f1f5f9;
        padding-top: 16px;
    }

    .pagination-container nav {
        display: flex;
        justify-content: flex-end;
        width: auto;
    }

    .pagination-container svg {
        width: 16px;
        height: 16px;
    }

    .pagination-container ul, 
    .pagination-container nav > div:first-child {
        display: none !important;
    }

    .pagination-container nav > div:last-child {
        display: flex;
        width: 100%;
        justify-content: flex-end;
    }

    .pagination-container nav span, 
    .pagination-container nav a {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 12px;
        margin: 0 2px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        color: #475569;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        background-color: #fff;
        transition: all 0.2s ease;
    }

    .pagination-container nav span[aria-current="page"] {
        background-color: #3b82f6;
        color: #fff;
        border-color: #3b82f6;
        box-shadow: 0 4px 10px rgba(59, 130, 246, 0.25);
        border-radius: 50%;
        width: 36px;
    }

    .pagination-container nav span[aria-disabled="true"] {
        background-color: #f8fafc;
        color: #cbd5e1;
        border-color: #e2e8f0;
    }

    .pagination-container nav a:hover {
        background-color: #f1f5f9;
        border-color: #cbd5e1;
        color: #0f172a;
    }

    /* Notifikasi Mengambang */
    .notif-popup {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        max-width: 450px;
        padding: 16px 20px;
        border-radius: 10px;
        font-weight: 600;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        justify-content: space-between;
        animation: slideInRight 0.3s ease-out forwards;
    }

    .notif-sukses { background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    .notif-gagal { background-color: #fee2e2; color: #7f1d1d; border: 1px solid #fecaca; }
    .notif-close { background: none; border: none; font-size: 18px; cursor: pointer; color: inherit; opacity: 0.7; margin-left: 15px; }
    .notif-close:hover { opacity: 1; }

    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    /* === PERBAIKAN BUG MODAL & DROPDOWN MELUBER === */
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
        padding: 20px;
        overflow-y: auto;
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
        overflow: visible !important; 
        max-height: none; 
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
        position: relative;
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
        color: #0f172a;
    }

    select.form-control {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23475569' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 16px;
        padding-right: 40px;
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
        transition: background 0.2s;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
    }
    .btn-simpan:hover { background-color: #059669; }

    .spinner {
        width: 18px;
        height: 18px;
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 0.8s linear infinite;
        display: inline-block;
    }

    @keyframes spin { to { transform: rotate(360deg); } }

    @media screen and (max-width: 768px) {
        .stats-grid { grid-template-columns: 1fr; gap: 12px; }
        .table-container { padding: 15px; overflow-x: auto; }
        table { min-width: 550px; }
        .modal-box { width: 90%; padding: 20px; }
        .radio-grid { grid-template-columns: 1fr; }
        .notif-popup { left: 20px; right: 20px; max-width: none; }
    }
</style>
@endpush

@section('konten')
    
    @if(session('sukses'))
        <div id="notifAlert" class="notif-popup notif-sukses">
            <span>{{ session('sukses') }}</span>
            <button class="notif-close" onclick="tutupNotif()">&times;</button>
        </div>
    @endif
    
    @if(session('gagal'))
        <div id="notifAlert" class="notif-popup notif-gagal">
            <span>{{ session('gagal') }}</span>
            <button class="notif-close" onclick="tutupNotif()">&times;</button>
        </div>
    @endif

    <!-- Skeleton Wrapper / Tampilan Utama -->
    <div id="mainDashboardContent">
        @if(Auth::user()->role == 'admin')
        <div class="rekap-container">
            <div class="kartu-rekap">
                <h3>Pengunjung Hari Ini</h3>
                <div class="angka">{{ $totalHariIni ?? 0 }} Orang</div>
                <div class="trend-naik">📅 Tanggal: {{ date('d M Y') }}</div>
            </div>
            <div class="kartu-rekap">
                <h3>Pengunjung Kemarin</h3>
                <div class="angka">{{ $totalKemarin ?? 0 }} Orang</div>
                <div class="trend-naik" style="color: #64748b;">
                    @if(($totalHariIni ?? 0) >= ($totalKemarin ?? 0))
                        📈 Meningkat dari kemarin!
                    @else
                        📉 Lebih sepi dari kemarin
                    @endif
                </div>
            </div>
        </div>
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
        @endif 

        @if(Auth::user()->role == 'kasir')
        <div class="action-area">
            <button class="btn-aksi" id="btnBukaModal">[+] TRANSAKSI BARU / CHECK-IN</button>
        </div>
        @endif

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
                    @forelse($logaktivitas as $item)
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
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #64748b; padding: 20px;">Belum ada aktivitas transaksi hari ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Bagian Pagination dengan Preview / Next -->
            <div class="pagination-container">
                <div style="font-size: 13px; color: #64748b; font-weight: 500;">
                    Menampilkan {{ $logaktivitas->firstItem() ?? 0 }} - {{ $logaktivitas->lastItem() ?? 0 }} dari {{ $logaktivitas->total() }} data
                </div>
                <div>
                    {{ $logaktivitas->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- SKELETON LOADER CONTAINER (Awalnya tersembunyi, muncul saat halaman dimuat/paging) -->
    <div id="skeletonLoader" style="display: none;">
        <div class="rekap-container">
            <div class="kartu-rekap"><div class="skeleton skeleton-title"></div><div class="skeleton skeleton-box"></div></div>
            <div class="kartu-rekap"><div class="skeleton skeleton-title"></div><div class="skeleton skeleton-box"></div></div>
        </div>
        <div class="stats-grid" style="margin-top: 20px;">
            <div class="card"><div class="skeleton skeleton-title"></div><div class="skeleton" style="height:32px; width:50%;"></div></div>
            <div class="card"><div class="skeleton skeleton-title"></div><div class="skeleton" style="height:32px; width:50%;"></div></div>
            <div class="card"><div class="skeleton skeleton-title"></div><div class="skeleton" style="height:32px; width:50%;"></div></div>
        </div>
        <div class="table-container" style="margin-top: 25px;">
            <div class="skeleton skeleton-title" style="width: 30%;"></div>
            <div class="skeleton" style="height: 250px; width: 100%; margin-top: 15px;"></div>
        </div>
    </div>

    <div class="modal-overlay" id="modalTransaksi">
        <div class="modal-box">
            <div class="modal-header">
                <h2>Form Transaksi & Check-in</h2>
                <button class="btn-close" id="btnTutupModal">&times;</button>
            </div>
            
            <form action="{{ route('transaksi.store') }}" method="POST" id="formTransaksi">
                @csrf
                <div class="form-group">
                    <label>Pilihan Aksi:</label>
                    <div class="radio-grid">
                        @foreach($hargaPaket as $hp)
                            @if(in_array(strtolower($hp->nama_paket), ['harian', 'checkin', 'perpanjang']))
                                <label>
                                    <input type="radio" name="tipe_kunjungan" value="{{ $hp->nama_paket }}" data-harga="{{ $hp->harga }}" 
                                    {{ $loop->first ? 'checked' : '' }}> 
                                    {{ ucfirst($hp->nama_paket) }}
                                </label>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div class="form-group" id="inputNama">
                    <label>Nama Pengunjung Harian:</label>
                    <input type="text" name="nama" class="form-control" placeholder="Masukkan nama...">
                </div>

                <div class="form-group" id="selectMemberCheckin" style="display: none;">
                    <label>Pilih Nama Member (Aktif):</label>
                    <select name="member_id" id="memberIdCheckin" class="form-control" disabled>
                        <option value="">-- Pilih Member --</option>
                        @foreach($daftarMember as $member)
                            <option value="{{ $member->id }}">{{ $member->nama_member }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" id="selectMemberPerpanjang" style="display: none;">
                    <label>Pilih Nama Member:</label>
                    <select name="member_id" id="memberIdPerpanjang" class="form-control" disabled>
                        <option value="">-- Pilih Member --</option>
                        @foreach($daftarMemberSemua as $member)
                            <option value="{{ $member->id }}">{{ $member->nama_member }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" id="inputNominal">
                    <label>Total Bayar (Rp):</label>
                    <input type="number" name="nominal" id="nominalInput" class="form-control" readonly>
                </div>

                <button type="submit" class="btn-simpan" id="btnSimpan">
                    <span>SIMPAN TRANSAKSI KASIR</span>
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Efek Animasi Skeleton saat klik Pagination atau Refresh Halaman
    document.addEventListener("DOMContentLoaded", function() {
        const mainContent = document.getElementById('mainDashboardContent');
        const skeletonLoader = document.getElementById('skeletonLoader');

        // Tangkap semua klik pada link pagination
        const paginationLinks = document.querySelectorAll('.pagination-container a');
        paginationLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Tampilkan skeleton, sembunyikan konten asli sejenak sebelum pindah halaman
                mainContent.style.display = 'none';
                skeletonLoader.style.display = 'block';
            });
        });
    });

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
    
    const formTransaksi = document.getElementById('formTransaksi');
    const btnSimpan = document.getElementById('btnSimpan');

    if (btnBuka) {
        btnBuka.addEventListener('click', () => modal.classList.add('show'));
    }
    if (btnTutup) {
        btnTutup.addEventListener('click', () => modal.classList.remove('show'));
    }

    radioTipe.forEach(radio => {
        radio.addEventListener('change', (e) => {
            fieldNominal.value = e.target.getAttribute('data-harga');

            inputNama.style.display = 'none';
            selectMemberCheckin.style.display = 'none';
            selectMemberPerpanjang.style.display = 'none';
            memberIdCheckin.disabled = true;
            memberIdPerpanjang.disabled = true;
            inputNominal.style.display = 'block';

            if (e.target.value === 'harian') {
                inputNama.style.display = 'block';
            } else if (e.target.value === 'checkin') {
                selectMemberCheckin.style.display = 'block';
                memberIdCheckin.disabled = false;
                inputNominal.style.display = 'none'; 
            } else if (e.target.value === 'perpanjang') {
                selectMemberPerpanjang.style.display = 'block';
                memberIdPerpanjang.disabled = false;
            }
        });
    });

    const firstRadio = document.querySelector('input[name="tipe_kunjungan"]:checked');
    if (firstRadio) {
        fieldNominal.value = firstRadio.getAttribute('data-harga');
        if (firstRadio.value === 'checkin') {
            inputNominal.style.display = 'none';
            selectMemberCheckin.style.display = 'block';
            memberIdCheckin.disabled = false;
        }
    }

    if (formTransaksi) {
        formTransaksi.addEventListener('submit', function() {
            btnSimpan.disabled = true;
            btnSimpan.innerHTML = '<div class="spinner"></div> Menyimpan...';
        });
    }

    function tutupNotif() {
        const notif = document.getElementById('notifAlert');
        if (notif) {
            notif.style.opacity = '0';
            notif.style.transition = 'opacity 0.3s ease';
            setTimeout(() => notif.remove(), 300);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const notif = document.getElementById('notifAlert');
        if (notif) {
            setTimeout(() => {
                tutupNotif();
            }, 4000);
        }
    });
</script>
@endpush