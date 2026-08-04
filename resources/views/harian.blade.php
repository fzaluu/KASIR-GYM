@extends('layouts.app')

@section('title', 'Pengunjung Harian')
@section('page_title', 'Pengunjung Harian')

@push('styles')
<style>
    .top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .btn-tambah {
        background-color: #10b981;
        color: #fff;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        font-size: 14px;
        cursor: pointer;
        font-weight: 600;
        transition: background-color 0.2s;
        box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2);
    }
    .btn-tambah:hover { background-color: #059669; }

    .filter-box form {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .input-filter {
        padding: 10px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        background-color: #fff;
        color: #0f172a;
    }
    .input-filter:focus { border-color: #38bdf8; }

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
        color: #0f172a;
    }

    th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 600;
    }

    tr:hover td { background-color: #f8fafc; }

    .action-btns {
        display: flex;
        gap: 8px;
    }

    .btn-edit {
        background-color: #f59e0b;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 600;
        transition: background-color 0.2s;
    }
    .btn-edit:hover { background-color: #d97706; }

    .btn-hapus {
        background-color: #ef4444;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 600;
        transition: background-color 0.2s;
    }
    .btn-hapus:hover { background-color: #dc2626; }

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

    .notif-sukses {
        background-color: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    .notif-gagal {
        background-color: #fee2e2;
        color: #7f1d1d;
        border: 1px solid #fecaca;
    }

    .notif-close {
        background: none;
        border: none;
        font-size: 18px;
        cursor: pointer;
        color: inherit;
        opacity: 0.7;
        margin-left: 15px;
    }
    .notif-close:hover { opacity: 1; }

    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    .modal-overlay {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background-color: rgba(15, 23, 42, 0.6);
        display: flex; justify-content: center; align-items: center;
        opacity: 0; pointer-events: none;
        transition: all 0.25s ease; z-index: 999;
    }

    .modal-overlay.show { opacity: 1; pointer-events: auto; }

    .modal-box {
        background-color: #fff; padding: 30px; border-radius: 12px;
        width: 450px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
        transform: scale(0.9); transition: all 0.25s ease;
    }

    .modal-overlay.show .modal-box { transform: scale(1); }

    .modal-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px;
    }

    .modal-header h2 { font-size: 18px; color: #0f172a; }
    .btn-close { background: none; border: none; font-size: 28px; cursor: pointer; color: #94a3b8; line-height: 1; }

    .form-group { margin-bottom: 16px; }
    .form-group label { display: block; font-size: 14px; font-weight: 600; margin-bottom: 6px; color: #334155; }
    .form-group input { width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; color: #0f172a; box-sizing: border-box; background-color: #fff; }
    .form-group input:focus { border-color: #38bdf8; }

    .btn-simpan {
        width: 100%; background-color: #38bdf8; color: white; border: none;
        padding: 12px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 15px;
        transition: background-color 0.2s;
    }
    .btn-simpan:hover { background-color: #0ea5e9; }
    
    .btn-simpan.loading {
        pointer-events: none;
        opacity: 0.85;
    }
    .pagination-btn:hover {
        border-color: #3b82f6 !important;
        color: #2563eb !important;
        background-color: #f8fafc !important;
    }

    @media screen and (max-width: 768px) {
        .top-bar { flex-direction: column; align-items: stretch; }
        .filter-box form { width: 100%; }
        .input-filter { flex: 1; min-width: 120px; }
        .table-container { padding: 12px; overflow-x: auto; }
        table { min-width: 550px; }
        .modal-box { width: 90%; padding: 20px; }
        .notif-popup { left: 20px; right: 20px; max-width: none; }
    }

    .skeleton {
        background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
        border-radius: 6px;
    }

    @keyframes shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
</style>
@endpush

@section('konten')

    {{-- Notifikasi Sukses --}}
    @if(session('sukses'))
        <div id="notifAlert" class="notif-popup notif-sukses">
            <span>{{ session('sukses') }}</span>
            <button class="notif-close" onclick="tutupNotif()">&times;</button>
        </div>
    @endif

    {{-- Notifikasi Gagal --}}
    @if(session('gagal'))
        <div id="notifAlert" class="notif-popup notif-gagal">
            <span>{{ session('gagal') }}</span>
            <button class="notif-close" onclick="tutupNotif()">&times;</button>
        </div>
    @endif

    <div class="top-bar">
        <button class="btn-tambah" onclick="bukaModalTambah()">[+] INPUT PENGUNJUNG HARIAN</button>
        
        <div class="filter-box">
            <form action="{{ route('harian.index') }}" method="GET" id="formFilter">
                <input type="text" id="inputCariHarian" name="cari" class="input-filter" placeholder="Cari nama..." value="{{ request('cari') }}" autocomplete="off">
                <input type="date" name="tanggal" class="input-filter" value="{{ request('tanggal', $tanggalTerpilih) }}">
            </form>
        </div>
    </div>

    <div class="table-container" id="wrapperTabelHarian">
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Waktu Masuk</th>
                <th>Nama Pengunjung</th>
                <th>Biaya Kunjungan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        {{-- Bagian Skeleton Loader --}}
        <tbody id="skeletonBody" style="display: none;">
            @for ($i = 0; $i < 5; $i++)
            <tr>
                <td><div class="skeleton" style="width: 20px; height: 16px;"></div></td>
                <td><div class="skeleton" style="width: 70px; height: 16px;"></div></td>
                <td><div class="skeleton" style="width: 140px; height: 16px;"></div></td>
                <td><div class="skeleton" style="width: 90px; height: 16px;"></div></td>
                <td><div class="skeleton" style="width: 100px; height: 24px;"></div></td>
            </tr>
            @endfor
        </tbody>

            <tbody id="tabelBodyHarian">
                @forelse($daftarHarian as $index => $row)
                <tr class="baris-harian">
                    <td>{{ ($daftarHarian->currentPage() - 1) * $daftarHarian->perPage() + $index + 1 }}</td>
                    <td>{{ date('H:i', strtotime($row->created_at)) }} WIB</td>
                    <td class="nama-target"><strong>{{ $row->nama_pelanggan }}</strong></td>
                    <td><span style="color: #10b981; font-weight: 600;">Rp {{ number_format($row->nominal, 0, ',', '.') }}</span></td>
                    <td>
                        <div class="action-btns">
                            <button class="btn-edit" data-harian="{{ json_encode($row) }}" onclick="bukaModalEdit(JSON.parse(this.getAttribute('data-harian')))">Edit</button>
                            
                            <form action="{{ route('harian.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Hapus data kunjungan harian ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-hapus">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="barisKosongBawaan">
                    <td colspan="5" style="text-align: center; color: #94a3b8; padding: 20px;">
                        Tidak ada pengunjung harian pada tanggal atau pencarian yang dipilih.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Bagian Pagination --}}
        @if ($daftarHarian->hasPages())
            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%; flex-wrap: wrap; gap: 12px; border-top: 1px solid #f1f5f9; padding-top: 20px; margin-top: 20px;">
                
                <div style="font-size: 13px; color: #64748b; font-weight: 500;">
                    Menampilkan <span style="font-weight: 600; color: #0f172a;">{{ $daftarHarian->firstItem() ?? 0 }}</span> - <span style="font-weight: 600; color: #0f172a;">{{ $daftarHarian->lastItem() ?? 0 }}</span> dari <span style="font-weight: 600; color: #0f172a;">{{ $daftarHarian->total() }}</span> data
                </div>

                <div style="display: flex; gap: 6px; align-items: center;">
                    @if ($daftarHarian->onFirstPage())
                        <span style="display: flex; align-items: center; justify-content: center; height: 38px; padding: 0 14px; border: 1px solid #e2e8f0; border-radius: 8px; color: #cbd5e1; background-color: #f8fafc; font-size: 13px; font-weight: 600; cursor: not-allowed; user-select: none;">
                            &laquo; Previous
                        </span>
                    @else
                        <a href="{{ $daftarHarian->previousPageUrl() }}" onclick="tampilkanSkeleton()" class="pagination-btn" style="display: flex; align-items: center; justify-content: center; height: 38px; padding: 0 14px; border: 1px solid #cbd5e1; border-radius: 8px; color: #475569; background-color: #fff; font-size: 13px; font-weight: 600; text-decoration: none; transition: all 0.2s ease;">
                            &laquo; Previous
                        </a>
                    @endif

                    @foreach ($daftarHarian->linkCollection() as $link)
                        @if ($link['label'] !== '&laquo; Previous' && $link['label'] !== 'Next &raquo;')
                            @if ($link['url'] === null)
                                <span style="display: flex; align-items: center; justify-content: center; height: 38px; padding: 0 6px; color: #94a3b8; font-size: 13px; font-weight: 500;">{!! $link['label'] !!}</span>
                            @else
                                @if ($link['active'])
                                    <span style="display: flex; align-items: center; justify-content: center; min-width: 38px; height: 38px; padding: 0 12px; border: 1px solid #3b82f6; border-radius: 8px; color: #fff; background-color: #3b82f6; font-size: 13px; font-weight: 600; box-shadow: 0 4px 10px rgba(59, 130, 246, 0.25); user-select: none;">
                                        {!! $link['label'] !!}
                                    </span>
                                @else
                                    <a href="{{ $link['url'] }}" onclick="tampilkanSkeleton()" class="pagination-btn" style="display: flex; align-items: center; justify-content: center; min-width: 38px; height: 38px; padding: 0 12px; border: 1px solid #cbd5e1; border-radius: 8px; color: #475569; background-color: #fff; font-size: 13px; font-weight: 600; text-decoration: none; transition: all 0.2s ease;">
                                        {!! $link['label'] !!}
                                    </a>
                                @endif
                            @endif
                        @endif
                    @endforeach

                    @if ($daftarHarian->hasMorePages())
                        <a href="{{ $daftarHarian->nextPageUrl() }}" onclick="tampilkanSkeleton()" class="pagination-btn" style="display: flex; align-items: center; justify-content: center; height: 38px; padding: 0 14px; border: 1px solid #cbd5e1; border-radius: 8px; color: #475569; background-color: #fff; font-size: 13px; font-weight: 600; text-decoration: none; transition: all 0.2s ease;">
                            Next &raquo;
                        </a>
                    @else
                        <span style="display: flex; align-items: center; justify-content: center; height: 38px; padding: 0 14px; border: 1px solid #e2e8f0; border-radius: 8px; color: #cbd5e1; background-color: #f8fafc; font-size: 13px; font-weight: 600; cursor: not-allowed; user-select: none;">
                            Next &raquo;
                        </span>
                    @endif
                </div>

            </div>
        @endif
    </div>

    <div class="modal-overlay" id="modalHarian">
        <div class="modal-box">
            <div class="modal-header">
                <h2 id="modalTitle">Input Pengunjung Harian</h2>
                <button class="btn-close" onclick="tutupModal()">&times;</button>
            </div>
            
            <form id="formHarian" action="" method="POST">
                @csrf
                <div id="methodField"></div>
                
                <div class="form-group">
                    <label>Nama Pengunjung:</label>
                    <input type="text" name="nama_pelanggan" id="inputNama" placeholder="Nama pengunjung harian..." required>
                </div>

                <div class="form-group">
                    <label>Nominal Bayar (Rp):</label>
                    <input type="number" name="nominal" id="inputNominal" min="0" value="{{ $hargaHarian }}" readonly>
                </div>

                <button type="submit" class="btn-simpan" id="btnSubmitForm">SIMPAN DATA</button>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    const modal = document.getElementById('modalHarian');
    const form = document.getElementById('formHarian');
    const modalTitle = document.getElementById('modalTitle');
    const methodField = document.getElementById('methodField');
    const inputNama = document.getElementById('inputNama');
    const inputNominal = document.getElementById('inputNominal');
    
    const hargaDefault = {{ $hargaHarian }};

    function bukaModalTambah() {
        modalTitle.innerText = "Input Pengunjung Harian";
        form.action = "{{ route('harian.store') }}";
        methodField.innerHTML = "";
        inputNama.value = "";
        inputNominal.value = hargaDefault; 
        modal.classList.add('show');
    }

    function bukaModalEdit(data) {
        modalTitle.innerText = "Edit Data Pengunjung";
        form.action = "/harian/" + data.id;
        methodField.innerHTML = `@method('PUT')`;
        inputNama.value = data.nama_pelanggan;
        inputNominal.value = data.nominal;
        modal.classList.add('show');
    }

    function tutupModal() { modal.classList.remove('show'); }

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

    function tampilkanSkeleton() {
        document.getElementById('tabelBodyHarian').style.display = 'none';
        document.getElementById('skeletonBody').style.display = 'table-row-group';
    }

    // Smooth Live Search menggunakan Fetch AJAX (Tanpa potong ketikan & Tanpa reload kedip)
    document.addEventListener('DOMContentLoaded', function() {
        const inputCari = document.getElementById('inputCariHarian');
        const inputTanggal = document.querySelector('input[name="tanggal"]');
        let searchTimer;

        if (inputCari) {
            inputCari.addEventListener('input', function() {
                clearTimeout(searchTimer);
                const keyword = this.value;
                const tanggalVal = inputTanggal ? inputTanggal.value : '';

                // Tampilkan skeleton tipis saat mulai mencari
                tampilkanSkeleton();

                // Beri jeda 300ms agar user selesai mengetik kata sambungannya ("ad" -> "adajika")
                searchTimer = setTimeout(() => {
                    const url = `{{ route('harian.index') }}?cari=${encodeURIComponent(keyword)}&tanggal=${encodeURIComponent(tanggalVal)}`;
                    
                    fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        // Ambil bagian tabel dari halaman yang di-fetch
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newTableContainer = doc.getElementById('wrapperTabelHarian') || doc.querySelector('.table-container');
                        
                        if (newTableContainer) {
                            document.getElementById('wrapperTabelHarian').innerHTML = newTableContainer.innerHTML;
                        }

                        // Kembalikan kursor ke input pencarian agar fokus tidak hilang saat mengetik
                        const newInputCari = document.getElementById('inputCariHarian');
                        if (newInputCari) {
                            newInputCari.focus();
                            // Taruh kursor di akhir teks input
                            const valLength = newInputCari.value.length;
                            newInputCari.setSelectionRange(valLength, valLength);
                        }
                    })
                    .catch(err => console.error('Gagal melakukan pencarian:', err));
                }, 300);
            });
        }

        if (inputTanggal) {
            inputTanggal.addEventListener('change', function() {
                tampilkanSkeleton();
                this.form.submit();
            });
        }
    });

    // Animasi Loading pada Tombol Simpan Data (Mempertahankan Warna Asli)
    const formHarian = document.getElementById('formHarian');
    if (formHarian) {
        formHarian.addEventListener('submit', function(e) {
            const btn = document.getElementById("btnSubmitForm");
            if (btn) {
                btn.classList.add("loading");
                btn.innerHTML = `
                    <svg width="18" height="18" viewBox="0 0 50 50" style="vertical-align: middle; margin-right: 6px;">
                        <circle cx="25" cy="25" r="20" fill="none" stroke="white" stroke-width="5" stroke-linecap="round" stroke-dasharray="31.4 31.4">
                            <animateTransform attributeName="type" type="rotate" repeatCount="indefinite" dur="0.8s" values="0 25 25;360 25 25"/>
                        </circle>
                    </svg>
                    Memproses...
                `;
            }
        });
    }
</script>
@endpush