@extends('layouts.app')

@section('title', 'Dashboard Utama - Virgo Gym')
@section('page_title', 'Dashboard')

@push('styles')
<!-- Choices.js CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">

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

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 25px;
    }

    .rekap-container {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
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

    /* === PAGINATION STYLING === */
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
        align-items: center;
    }

    .pagination-container nav > div {
        display: flex;
        gap: 6px;
        align-items: center;
    }

    .pagination-container svg {
        width: 16px;
        height: 16px;
    }

    .pagination-container nav a,
    .pagination-container nav span[aria-current="page"],
    .pagination-container nav span[aria-disabled="true"] {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 38px;
        height: 38px;
        padding: 0 14px;
        border: 1px solid #cbd5e1;
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
    }

    .pagination-container nav span[aria-disabled="true"] {
        background-color: #f8fafc;
        color: #cbd5e1;
        border-color: #e2e8f0;
        cursor: not-allowed;
    }

    .pagination-container nav a:hover {
        background-color: #f1f5f9;
        border-color: #94a3b8;
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

    /* === MODAL & FORM STYLING === */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(15, 23, 42, 0.6);
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding-top: 40px;
        padding-bottom: 40px;
        overflow-y: auto;
        z-index: 9999;
        opacity: 0;
        pointer-events: none;
        transition: all 0.25s ease;
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
        max-width: 100%;
        margin: 0 auto;
        overflow-y: visible;
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
        transform: scale(0.9);
        transition: all 0.25s ease;
        padding-bottom: 40px;
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
        height: 46px;
        padding: 10px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        background-color: #fff;
        box-sizing: border-box;
        color: #0f172a;
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

    /* === CHOICES.JS CUSTOM STYLING === */
    .choices { margin-bottom: 0; }
    .choices__inner {
        min-height: 46px;
        padding: 8px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background-color: #fff;
        font-size: 14px;
        color: #0f172a;
    }
    .choices.is-focused .choices__inner,
    .choices.is-open .choices__inner {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    }
    .choices__list--dropdown {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background-color: #fff;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        z-index: 10000;
    }

    @media screen and (max-width: 768px) {
        .stats-grid { grid-template-columns: 1fr; gap: 12px; }
        .table-container { padding: 15px; overflow-x: auto; }
        table { min-width: 550px; }
        .modal-box { width: 95%; padding: 20px; padding-bottom: 40px; }
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

    <!-- Bagian Atas: Statistik & Tombol Aksi (Tetap Stabil) -->
    @if(Auth::user()->isAdmin())
    <div class="rekap-container">
        <div class="kartu-rekap">
            <h3>Pengunjung Hari Ini</h3>
            <div class="angka">{{ $totalHariIni ?? 0 }} Orang</div>
            <div class="trend-naik">Tanggal: {{ date('d M Y') }}</div>
        </div>
        <div class="kartu-rekap">
            <h3>Pengunjung Kemarin</h3>
            <div class="angka">{{ $totalKemarin ?? 0 }} Orang</div>
            <div class="trend-naik" style="color: #64748b;">
                @if(($totalHariIni ?? 0) >= ($totalKemarin ?? 0))
                    Meningkat dari kemarin!
                @else
                    Lebih sepi dari kemarin
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

    @if(Auth::user()->isKasir())
    <div class="action-area">
        <button class="btn-aksi" id="btnBukaModal">[+] TRANSAKSI BARU / CHECK-IN</button>
    </div>
    @endif

    <!-- Bagian Tabel & Log Aktivitas (Hanya bagian ini yang memiliki Skeleton) -->
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
            
            {{-- Skeleton Table Body (Disembunyikan secara default) --}}
            <tbody id="skeletonTableBody" style="display: none;">
                @for ($i = 0; $i < 5; $i++)
                <tr>
                    <td><div class="skeleton" style="width: 70px; height: 16px;"></div></td>
                    <td><div class="skeleton" style="width: 140px; height: 16px;"></div></td>
                    <td><div class="skeleton" style="width: 120px; height: 16px;"></div></td>
                    <td><div class="skeleton" style="width: 90px; height: 16px;"></div></td>
                </tr>
                @endfor
            </tbody>

            {{-- Data Asli Tabel --}}
            <tbody id="mainTableBody">
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

        <!-- Bagian Pagination -->
        <div class="pagination-container">
            {{ $logaktivitas->links('vendor.pagination.custom') }}
        </div>
    </div>

    <!-- Modal Transaksi Kasir -->
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
                                    <input type="radio" name="tipe_kunjungan" value="{{ strtolower($hp->nama_paket) }}" data-harga="{{ $hp->harga }}"
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
<!-- Choices.js JS -->
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<script>
    // Event listener untuk memicu skeleton loader khusus pada tabel saat pagination diklik
    document.addEventListener("DOMContentLoaded", function() {
        const mainTableBody = document.getElementById('mainTableBody');
        const skeletonTableBody = document.getElementById('skeletonTableBody');

        const paginationLinks = document.querySelectorAll('.pagination-container a');
        paginationLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                if (mainTableBody && skeletonTableBody) {
                    mainTableBody.style.display = 'none';
                    skeletonTableBody.style.display = 'table-row-group';
                }
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

    // Inisialisasi Choices.js
    const choicesCheckin = new Choices(memberIdCheckin, {
        searchEnabled: true,
        itemSelectText: '',
        shouldSort: false,
        position: 'bottom',
        noResultsText: 'Member tidak ditemukan',
        noChoicesText: 'Tidak ada member'
    });

    const choicesPerpanjang = new Choices(memberIdPerpanjang, {
        searchEnabled: true,
        itemSelectText: '',
        shouldSort: false,
        position: 'bottom',
        noResultsText: 'Member tidak ditemukan',
        noChoicesText: 'Tidak ada member'
    });

    function aturTampilanForm(tipe, harga) {
        fieldNominal.value = harga;

        inputNama.style.display = 'none';
        selectMemberCheckin.style.display = 'none';
        selectMemberPerpanjang.style.display = 'none';
        choicesCheckin.disable();
        choicesPerpanjang.disable();
        inputNominal.style.display = 'block';

        if (tipe === 'harian') {
            inputNama.style.display = 'block';
        } else if (tipe === 'checkin') {
            selectMemberCheckin.style.display = 'block';
            choicesCheckin.enable();
            inputNominal.style.display = 'none';
        } else if (tipe === 'perpanjang') {
            selectMemberPerpanjang.style.display = 'block';
            choicesPerpanjang.enable();
        }
    }

    if (btnBuka) {
        btnBuka.addEventListener('click', () => {
            modal.classList.add('show');
            const modalBox = modal.querySelector('.modal-box');
            if (modalBox) modalBox.scrollTop = 0;

            const activeRadio = document.querySelector('input[name="tipe_kunjungan"]:checked');
            if (activeRadio) {
                aturTampilanForm(activeRadio.value.toLowerCase(), activeRadio.getAttribute('data-harga'));
            }
        });
    }

    if (btnTutup) {
        btnTutup.addEventListener('click', () => modal.classList.remove('show'));
    }

    radioTipe.forEach(radio => {
        radio.addEventListener('change', (e) => {
            aturTampilanForm(e.target.value.toLowerCase(), e.target.getAttribute('data-harga'));
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const firstRadio = document.querySelector('input[name="tipe_kunjungan"]:checked');
        if (firstRadio) {
            aturTampilanForm(firstRadio.value.toLowerCase(), firstRadio.getAttribute('data-harga'));
        }
    });

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