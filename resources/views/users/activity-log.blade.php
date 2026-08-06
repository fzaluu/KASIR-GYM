@extends('layouts.app')

@section('title', 'Activity Log')
@section('page_title', 'Riwayat Aktivitas Sistem (Activity Log)')

@push('styles')
<!-- Choices.js CSS untuk Dropdown dengan Live Search -->
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

    /* Choices.js Custom Adjustment agar serasi */
    .choices { margin-bottom: 0; min-width: 200px; }
    .choices__inner {
        min-height: 42px;
        padding: 4px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        background-color: #fff;
        font-size: 13px;
        color: #0f172a;
    }

    /* Modern Table Hover Effect & Styles */
    .modern-table tbody tr {
        transition: background-color 0.15s ease-in-out;
    }
    .modern-table tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Badge Colors Definition */
    .badge-login { background: #dcfce7; color: #15803d; }
    .badge-logout { background: #fee2e2; color: #b91c1c; }
    .badge-tambah { background: #dbeafe; color: #1d4ed8; }
    .badge-edit { background: #ffedd5; color: #c2410c; }
    .badge-hapus { background: #7f1d1d; color: #fee2e2; }
    .badge-reset-password { background: #f3e8ff; color: #7e22ce; }
    .badge-transaksi { background: #dcfce7; color: #15803d; }
    .badge-default { background: #f1f5f9; color: #475569; }
</style>
@endpush

@section('konten')
<div style="display: flex; flex-direction: column; gap: 24px; background-color: #f8fafc; padding: 24px; min-height: 100vh;">

    <!-- HEADER -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: #0f172a; margin: 0 0 4px 0;">Activity Log</h1>
            <p style="font-size: 14px; color: #64748b; margin: 0;">Riwayat seluruh aktivitas penting pengguna di dalam sistem.</p>
        </div>
    </div>

    <!-- SUMMARY CARD (4 Cards) -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <!-- Card 1: Total Aktivitas -->
        <div style="background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.05); display: flex; align-items: center; gap: 16px;">
            <div style="background: #eff6ff; color: #2563eb; padding: 12px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            </div>
            <div>
                <p style="font-size: 12px; font-weight: 600; color: #64748b; margin: 0 0 4px 0; text-transform: uppercase;">Total Aktivitas</p>
                <h3 style="font-size: 20px; font-weight: 700; color: #0f172a; margin: 0;">{{ $totalAktivitas ?? 0 }}</h3>
            </div>
        </div>

        <!-- Card 2: Login Hari Ini -->
        <div style="background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.05); display: flex; align-items: center; gap: 16px;">
            <div style="background: #f0fdf4; color: #16a34a; padding: 12px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
            </div>
            <div>
                <p style="font-size: 12px; font-weight: 600; color: #64748b; margin: 0 0 4px 0; text-transform: uppercase;">Login Hari Ini</p>
                <h3 style="font-size: 20px; font-weight: 700; color: #0f172a; margin: 0;">{{ $loginHariIni ?? 0 }}</h3>
            </div>
        </div>

        <!-- Card 3: User Aktif -->
        <div style="background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.05); display: flex; align-items: center; gap: 16px;">
            <div style="background: #faf5ff; color: #9333ea; padding: 12px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <div>
                <p style="font-size: 12px; font-weight: 600; color: #64748b; margin: 0 0 4px 0; text-transform: uppercase;">User Aktif</p>
                <h3 style="font-size: 20px; font-weight: 700; color: #0f172a; margin: 0;">{{ $userAktif ?? 0 }}</h3>
            </div>
        </div>

        <!-- Card 4: Perubahan Data -->
        <div style="background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.05); display: flex; align-items: center; gap: 16px;">
            <div style="background: #fff7ed; color: #ea580c; padding: 12px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </div>
            <div>
                <p style="font-size: 12px; font-weight: 600; color: #64748b; margin: 0 0 4px 0; text-transform: uppercase;">Perubahan Data</p>
                <h3 style="font-size: 20px; font-weight: 700; color: #0f172a; margin: 0;">{{ $perubahanData ?? 0 }}</h3>
            </div>
        </div>
    </div>

    <!-- FILTER CARD -->
    <div style="background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.05);">
        <form id="filterForm" action="{{ route('activity-log.index') }}" method="GET" style="display: flex; flex-direction: column; gap: 16px;">
            
            <!-- Main Filter Controls -->
            <div style="display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end;">
                <div style="flex: 1; min-width: 220px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px; color: #475569;">Pilih User</label>
                    <select name="user_id" id="selectUser">
                        <option value="">Semua User</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ ($userId ?? '') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div style="flex: 1; min-width: 200px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px; color: #475569;">Kategori Aktivitas</label>
                    <select name="aksi" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; height: 42px; background-color: #fff; color: #0f172a;">
                        <option value="">Semua Aksi</option>
                        @foreach($listAksi as $act)
                            <option value="{{ $act }}" {{ ($aksi ?? '') == $act ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $act)) }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="flex: 1; min-width: 160px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px; color: #475569;">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai ?? '' }}" style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; height: 42px; box-sizing: border-box;">
                </div>

                <div style="flex: 1; min-width: 160px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px; color: #475569;">Tanggal Akhir</label>
                    <input type="date" name="tanggal_selesai" value="{{ $tanggalSelesai ?? '' }}" style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; height: 42px; box-sizing: border-box;">
                </div>

                <div style="display: flex; gap: 8px; align-items: flex-end;">
                    <button type="submit" style="padding: 0 20px; background: #2563eb; color: white; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; height: 42px; display: flex; align-items: center; justify-content: center; transition: background 0.2s;">Filter</button>
                    <a href="{{ route('activity-log.index') }}" style="padding: 0 16px; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 600; display: flex; align-items: center; justify-content: center; height: 42px; box-sizing: border-box;">Reset</a>
                </div>
            </div>

            <!-- Quick Filter Badges / Pills -->
            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; border-top: 1px solid #f1f5f9; padding-top: 12px;">
                <span style="font-size: 12px; font-weight: 600; color: #64748b; margin-right: 4px;">Quick Filter:</span>
                <button type="button" class="quick-filter-btn" data-range="today" style="padding: 4px 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 20px; font-size: 12px; color: #475569; cursor: pointer;">Hari Ini</button>
                <button type="button" class="quick-filter-btn" data-range="7days" style="padding: 4px 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 20px; font-size: 12px; color: #475569; cursor: pointer;">7 Hari</button>
                <button type="button" class="quick-filter-btn" data-range="30days" style="padding: 4px 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 20px; font-size: 12px; color: #475569; cursor: pointer;">30 Hari</button>
                <button type="button" class="quick-filter-btn" data-range="thismonth" style="padding: 4px 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 20px; font-size: 12px; color: #475569; cursor: pointer;">Bulan Ini</button>
            </div>

        </form>
    </div>

    <!-- TABEL ACTIVITY LOG -->
    <div style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.05);">
        <div style="overflow-x: auto;">
            <table class="modern-table" style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px; color: #1e293b;">
                <thead style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #475569; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em;">
                    <tr>
                        <th style="padding: 14px 20px; font-weight: 600;">Waktu</th>
                        <th style="padding: 14px 20px; font-weight: 600;">Nama User</th>
                        <th style="padding: 14px 20px; font-weight: 600;">Modul</th>
                        <th style="padding: 14px 20px; font-weight: 600;">Aksi</th>
                        <th style="padding: 14px 20px; font-weight: 600;">Deskripsi Aktivitas</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($logs as $log)
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 14px 20px; color: #64748b; font-size: 13px; white-space: nowrap;">
                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td style="padding: 14px 20px; font-weight: 600; color: #0f172a;">
                            {{ $log->user?->name ?? 'Sistem / Tamu' }}
                        </td>
                        <td style="padding: 14px 20px; color: #475569; font-size: 13px;">
                            {{ $log->modul ?? '-' }}
                        </td>
                        <td style="padding: 14px 20px;">
                            @php
                                $aksiLower = strtolower($log->aksi);
                                $badgeClass = 'badge-default';
                                if (str_contains($aksiLower, 'login')) $badgeClass = 'badge-login';
                                elseif (str_contains($aksiLower, 'logout')) $badgeClass = 'badge-logout';
                                elseif (str_contains($aksiLower, 'tambah') || str_contains($aksiLower, 'create')) $badgeClass = 'badge-tambah';
                                elseif (str_contains($aksiLower, 'edit') || str_contains($aksiLower, 'update')) $badgeClass = 'badge-edit';
                                elseif (str_contains($aksiLower, 'hapus') || str_contains($aksiLower, 'delete')) $badgeClass = 'badge-hapus';
                                elseif (str_contains($aksiLower, 'reset_password') || str_contains($aksiLower, 'password')) $badgeClass = 'badge-reset-password';
                                elseif (str_contains($aksiLower, 'transaksi') || str_contains($aksiLower, 'payment')) $badgeClass = 'badge-transaksi';
                            @endphp
                            <span style="padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; display: inline-block;" class="{{ $badgeClass }}">
                                {{ ucfirst(str_replace('_', ' ', $log->aksi)) }}
                            </span>
                        </td>
                        <td style="padding: 14px 20px; color: #334155; font-size: 13px;">
                            {{ $log->deskripsi }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="padding: 48px 20px; text-align: center;">
                            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 12px;">
                                <svg width="48" height="48" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                <p style="font-size: 14px; color: #64748b; margin: 0;">Belum ada aktivitas yang ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div style="padding: 16px;">
                {{ $logs->withQueryString()->links('vendor.pagination.custom') }}
            </div>
        </div>

        
    </div>

</div>
@endsection

@push('scripts')
<!-- Choices.js JS -->
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const selectUser = document.getElementById('selectUser');
        if (selectUser) {
            new Choices(selectUser, { searchEnabled: true, itemSelectText: '', shouldSort: false });
        }

        const filterForm = document.getElementById('filterForm');
        const tableBody = document.getElementById('tableBody');

        function tampilkanSkeleton() {
            if (tableBody) {
                let skeletonRows = '';
                // 5 baris skeleton sesuai dengan limit pagination 5 data per halaman
                for (let i = 0; i < 5; i++) {
                    skeletonRows += `
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 14px 20px;"><div class="skeleton-box" style="height: 16px; width: 130px;"></div></td>
                            <td style="padding: 14px 20px;"><div class="skeleton-box" style="height: 16px; width: 120px;"></div></td>
                            <td style="padding: 14px 20px;"><div class="skeleton-box" style="height: 16px; width: 90px;"></div></td>
                            <td style="padding: 14px 20px;"><div class="skeleton-box" style="height: 22px; width: 80px; border-radius: 20px;"></div></td>
                            <td style="padding: 14px 20px;"><div class="skeleton-box" style="height: 16px; width: 220px;"></div></td>
                        </tr>
                    `;
                }
                tableBody.innerHTML = skeletonRows;
            }
        }

        // Munculkan skeleton saat form filter utama disubmit
        if (filterForm) {
            filterForm.addEventListener('submit', function() {
                tampilkanSkeleton();
            });
        }

        // Quick Filter Handler
        const quickFilterBtns = document.querySelectorAll('.quick-filter-btn');
        const tanggalMulaiInput = document.querySelector('input[name="tanggal_mulai"]');
        const tanggalSelesaiInput = document.querySelector('input[name="tanggal_selesai"]');

        quickFilterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const range = this.getAttribute('data-range');
                const today = new Date();
                let start = new Date();

                if (range === 'today') {
                    start = today;
                } else if (range === '7days') {
                    start.setDate(today.getDate() - 7);
                } else if (range === '30days') {
                    start.setDate(today.getDate() - 30);
                } else if (range === 'thismonth') {
                    start = new Date(today.getFullYear(), today.getMonth(), 1);
                }

                const formatDate = (date) => {
                    let d = new Date(date),
                        month = '' + (d.getMonth() + 1),
                        day = '' + d.getDate(),
                        year = d.getFullYear();
                    if (month.length < 2) month = '0' + month;
                    if (day.length < 2) day = '0' + day;
                    return [year, month, day].join('-');
                };

                if (tanggalMulaiInput && tanggalSelesaiInput) {
                    tanggalMulaiInput.value = formatDate(start);
                    tanggalSelesaiInput.value = formatDate(today);
                    tampilkanSkeleton();
                    filterForm.submit();
                }
            });
        });

        // Tangkap klik pagination, tampilkan skeleton, lalu pindah halaman secara mulus
        // Tangkap klik pagination secara lebih luas (mencakup semua tag a di dalam area pagination)
        document.addEventListener('click', function(e) {
            let paginationLink = e.target.closest('.pagination a, nav a, [class*="pagination"] a, [class*="paginator"] a');
            
            // Pastikan yang diklik benar-benar link pagination dan bukan link eksternal/anchor lain
            if (paginationLink && paginationLink.href) {
                e.preventDefault();
                let targetUrl = paginationLink.href;
                
                tampilkanSkeleton(); // Jalankan animasi skeleton pada tabel
                
                // Beri jeda agar skeleton sempat dirender ke layar sebelum pindah halaman
                setTimeout(function() {
                    window.location.href = targetUrl;
                }, 150);
            }
        });
    });
</script>
@endpush