@extends('layouts.app')

@section('title', 'Riwayat Transaksi User - Virgo Gym')
@section('page_title', 'Riwayat Transaksi Pengguna Sistem')

@push('styles')
<!-- Choices.js CSS untuk Dropdown dengan Search -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">

<style>
    @keyframes skeleton-pulse {
        0% { opacity: 1; }
        50% { opacity: 0.4; }
        100% { opacity: 1; }
    }
    .skeleton-box {
        background: #e2e8f0;
        border-radius: 6px;
        animation: skeleton-pulse 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    /* Quick Filter Button Style */
    .quick-filter-btn {
        padding: 6px 12px;
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s;
    }
    .quick-filter-btn:hover, .quick-filter-btn.active {
        background: #2563eb;
        color: white;
        border-color: #2563eb;
    }

    /* Choices.js Custom Adjustment */
    .choices { margin-bottom: 0; min-width: 180px; }
    .choices__inner {
        min-height: 38px;
        padding: 4px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        background-color: #fff;
        font-size: 13px;
        color: #0f172a;
    }
</style>
@endpush

@section('konten')
<div style="display: flex; flex-direction: column; gap: 20px;">

    <!-- Stat Cards Statistik -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
        <div style="background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <div style="font-size: 13px; color: #64748b; font-weight: 600; margin-bottom: 6px;">Total Transaksi (Sesuai Filter)</div>
            <div style="font-size: 24px; font-weight: 700; color: #0f172a;">{{ number_format($totalTransaksiCount) }}</div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <div style="font-size: 13px; color: #64748b; font-weight: 600; margin-bottom: 6px;">Total Pendapatan (Sesuai Filter)</div>
            <div style="font-size: 24px; font-weight: 700; color: #16a34a;">Rp {{ number_format($totalPendapatanSum, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Filter Form & Quick Filter -->
    <div style="background: white; padding: 16px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; flex-direction: column; gap: 12px;">
        
        <!-- Tombol Quick Filter -->
        <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
            <span style="font-size: 12px; font-weight: 600; color: #64748b; margin-right: 4px;">Quick Filter:</span>
            <a href="{{ route('users.transaksi-riwayat', ['quick' => 'hari_ini']) }}" class="quick-filter-btn {{ ($quick ?? '') === 'hari_ini' ? 'active' : '' }}">Hari Ini</a>
            <a href="{{ route('users.transaksi-riwayat', ['quick' => '7_hari']) }}" class="quick-filter-btn {{ ($quick ?? '') === '7_hari' ? 'active' : '' }}">7 Hari</a>
            <a href="{{ route('users.transaksi-riwayat', ['quick' => '30_hari']) }}" class="quick-filter-btn {{ ($quick ?? '') === '30_hari' ? 'active' : '' }}">30 Hari</a>
            <a href="{{ route('users.transaksi-riwayat', ['quick' => 'bulan_ini']) }}" class="quick-filter-btn {{ ($quick ?? '') === 'bulan_ini' ? 'active' : '' }}">Bulan Ini</a>
        </div>

        <form id="filterForm" action="{{ route('users.transaksi-riwayat') }}" method="GET" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
            <div>
                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px; color: #475569;">Pilih Kasir</label>
                <select name="user_id" id="selectKasir">
                    <option value="">Semua Kasir</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ ($userId ?? '') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px; color: #475569;">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai ?? '' }}" style="padding: 7px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; height: 38px;">
            </div>
            <div>
                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px; color: #475569;">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" value="{{ $tanggalSelesai ?? '' }}" style="padding: 7px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; height: 38px;">
            </div>
            <div>
                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px; color: #475569;">Tipe Transaksi</label>
                <select name="tipe_transaksi" id="selectTipe">
                    <option value="">Semua Tipe</option>
                    <option value="Harian" {{ ($tipeTransaksi ?? '') == 'Harian' ? 'selected' : '' }}>Harian</option>
                    <option value="Checkin" {{ ($tipeTransaksi ?? '') == 'Checkin' ? 'selected' : '' }}>Checkin</option>
                    <option value="Perpanjang" {{ ($tipeTransaksi ?? '') == 'Perpanjang' ? 'selected' : '' }}>Perpanjang</option>
                    <option value="Sewa PT" {{ ($tipeTransaksi ?? '') == 'Sewa PT' ? 'selected' : '' }}>Sewa PT</option>
                </select>
            </div>
            <div style="display: flex; gap: 6px;">
                <button type="submit" style="padding: 9px 16px; background: #2563eb; color: white; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; height: 38px;">Filter</button>
                <a href="{{ route('users.transaksi-riwayat') }}" style="padding: 9px 14px; background: #64748b; color: white; border-radius: 6px; text-decoration: none; font-size: 13px; display: flex; align-items: center; height: 38px;">Reset</a>
            </div>
        </form>
    </div>

    <!-- Tabel Riwayat -->
    <div style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px; color: #1e293b;">
            <thead style="background: #f1f5f9; border-bottom: 1px solid #e2e8f0; color: #475569; font-size: 13px;">
                <tr>
                    <th style="padding: 14px 16px;">Waktu</th>
                    <th style="padding: 14px 16px;">Kasir</th>
                    <th style="padding: 14px 16px;">No. Invoice</th>
                    <th style="padding: 14px 16px;">Tipe</th>
                    <th style="padding: 14px 16px;">Nama Pelanggan</th>
                    <th style="padding: 14px 16px; text-align: right;">Nominal</th>
                    <th style="padding: 14px 16px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                @forelse($transaksi as $t)
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    <td style="padding: 14px 16px; color: #64748b; font-size: 13px;">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                    <td style="padding: 14px 16px; font-weight: 600;">{{ $t->user?->name ?? 'Sistem / Umum' }}</td>
                    <td style="padding: 14px 16px; color: #0284c7; font-weight: 600;">{{ $t->nomor_invoice ?? '-' }}</td>
                    <td style="padding: 14px 16px;">
                        <span style="padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #e0f2fe; color: #0284c7;">
                            {{ ucfirst($t->tipe_transaksi) }}
                        </span>
                    </td>
                    <td style="padding: 14px 16px;">{{ $t->nama_pelanggan ?? '-' }}</td>
                    <td style="padding: 14px 16px; text-align: right; font-weight: 600; color: #16a34a;">Rp {{ number_format($t->nominal, 0, ',', '.') }}</td>
                    <td style="padding: 14px 16px; text-align: center;">
                        <button type="button" onclick='bukaModalDetail(@json($t))' style="padding: 6px 12px; background: #d97706; color: white; border: none; border-radius: 6px; font-size: 12px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                            👁 Detail
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding: 30px; text-align: center; color: #64748b;">Tidak ada data riwayat transaksi ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div style="padding: 16px;">
            {{ $transaksi->links('vendor.pagination.custom') }}
        </div>
    </div>

</div>

<!-- MODAL DETAIL TRANSAKSI -->
<div id="modalDetailTransaksi" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(15,23,42,0.6); backdrop-filter: blur(6px); z-index: 1000; justify-content: center; align-items: center;">
    <div style="background: white; padding: 24px; border-radius: 12px; width: 100%; max-width: 440px; color: #1e293b;">
        <h3 style="margin-bottom: 16px; font-size: 18px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">Detail Transaksi</h3>
        
        <div style="display: flex; flex-direction: column; gap: 10px; font-size: 14px; margin-bottom: 20px;">
            <div><strong>Invoice &nbsp; &nbsp; &nbsp; &nbsp;:</strong> <span id="dtl_invoice" style="color: #0284c7; font-weight: 600;"></span></div>
            <div><strong>Nama Kasir &nbsp;:</strong> <span id="dtl_kasir"></span></div>
            <div><strong>Tanggal &nbsp; &nbsp; &nbsp;:</strong> <span id="dtl_tanggal"></span></div>
            <div><strong>Member &nbsp; &nbsp; &nbsp; :</strong> <span id="dtl_member"></span></div>
            <div><strong>Jenis &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;:</strong> <span id="dtl_jenis"></span></div>
            <div><strong>Nominal &nbsp; &nbsp; &nbsp;:</strong> <span id="dtl_nominal" style="color: #16a34a; font-weight: 700;"></span></div>
        </div>

        <div style="display: flex; justify-content: flex-end;">
            <button type="button" onclick="tutupModalDetail()" style="padding: 8px 16px; background: #64748b; color: white; border: none; border-radius: 6px; cursor: pointer;">Tutup</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Choices.js JS -->
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Inisialisasi Choices.js untuk Kasir & Tipe Transaksi
        const selectKasir = document.getElementById('selectKasir');
        if (selectKasir) {
            new Choices(selectKasir, { searchEnabled: true, itemSelectText: '', shouldSort: false });
        }

        const selectTipe = document.getElementById('selectTipe');
        if (selectTipe) {
            new Choices(selectTipe, { searchEnabled: true, itemSelectText: '', shouldSort: false });
        }

        const filterForm = document.getElementById('filterForm');
        const tableBody = document.getElementById('tableBody');

        if (filterForm) {
            filterForm.addEventListener('submit', function() {
                tampilkanSkeleton();
            });
        }

        document.querySelectorAll('.pagination a').forEach(link => {
            link.addEventListener('click', function() {
                tampilkanSkeleton();
            });
        });

        function tampilkanSkeleton() {
            if (tableBody) {
                let skeletonRows = '';
                for (let i = 0; i < 5; i++) {
                    skeletonRows += `
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 14px 16px;"><div class="skeleton-box" style="height: 16px; width: 100px;"></div></td>
                            <td style="padding: 14px 16px;"><div class="skeleton-box" style="height: 16px; width: 120px;"></div></td>
                            <td style="padding: 14px 16px;"><div class="skeleton-box" style="height: 16px; width: 90px;"></div></td>
                            <td style="padding: 14px 16px;"><div class="skeleton-box" style="height: 20px; width: 70px; border-radius: 20px;"></div></td>
                            <td style="padding: 14px 16px;"><div class="skeleton-box" style="height: 16px; width: 110px;"></div></td>
                            <td style="padding: 14px 16px; text-align: right;"><div class="skeleton-box" style="height: 16px; width: 80px; margin-left: auto;"></div></td>
                            <td style="padding: 14px 16px; text-align: center;"><div class="skeleton-box" style="height: 24px; width: 60px; margin: auto;"></div></td>
                        </tr>
                    `;
                }
                tableBody.innerHTML = skeletonRows;
            }
        }
    });

    function bukaModalDetail(trx) {
        document.getElementById('dtl_invoice').textContent = trx.nomor_invoice || '-';
        document.getElementById('dtl_kasir').textContent = trx.user ? trx.user.name : 'Sistem / Umum';
        document.getElementById('dtl_tanggal').textContent = new Date(trx.created_at).toLocaleString('id-ID');
        document.getElementById('dtl_member').textContent = trx.member ? trx.member.nama_member : (trx.nama_pelanggan || '-');
        document.getElementById('dtl_jenis').textContent = trx.tipe_transaksi;
        document.getElementById('dtl_nominal').textContent = 'Rp ' + Number(trx.nominal).toLocaleString('id-ID');

        document.getElementById('modalDetailTransaksi').style.display = 'flex';
    }

    function tutupModalDetail() {
        document.getElementById('modalDetailTransaksi').style.display = 'none';
    }
</script>
@endpush