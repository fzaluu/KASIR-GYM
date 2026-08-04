@extends('layouts.app')

@section('title', 'Manajemen Pelatih & Pengguna - Virgo Gym')
@section('page_title', 'Manajemen Pelatih & Pengguna Jasa (PT)')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/choices.min.css') }}">
<style>
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
    .top-bar { display: flex; justify-content: space-between; align-items: center; gap: 15px; flex-wrap: wrap; margin-bottom: 20px; }
    .section-block { margin: 30px 0 15px 0; display: flex; flex-direction: column; gap: 8px; align-items: flex-start; }
    .section-title { font-size: 18px; font-weight: 700; color: #1e293b; }
    
    .btn-tambah { background-color: #38bdf8; color: #fff; border: none; padding: 12px 20px; border-radius: 8px; font-size: 14px; cursor: pointer; font-weight: 600; box-shadow: 0 4px 6px -1px rgba(56, 189, 248, 0.2); }
    .btn-tambah:hover { background-color: #0ea5e9; }
    .btn-tambah-user { background-color: #10b981; color: #fff; border: none; padding: 12px 20px; border-radius: 8px; font-size: 14px; cursor: pointer; font-weight: 600; }
    .btn-tambah-user:hover { background-color: #059669; }
    
    .input-filter { padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; background-color: #fff; width: 220px; color: #0f172a; }
    .input-filter:focus { border-color: #38bdf8; }

    .table-container { background-color: #fff; padding: 24px; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; margin-bottom: 20px; overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; text-align: left; min-width: 650px; }
    th, td { padding: 12px 10px; border-bottom: 1px solid #e2e8f0; font-size: 13px; vertical-align: middle; color: #0f172a; }
    th { background-color: #f8fafc; color: #475569; font-weight: 600; }
    tr:hover td { background-color: #f8fafc; }

    .badge { padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
    .badge-hadir { background-color: #d1fae5; color: #065f46; }
    .badge-absen { background-color: #fee2e2; color: #991b1b; }
    .badge-waktu { background-color: #f1f5f9; color: #475569; font-weight: normal; }

    /* Notif Popup ala Member */
    .notif-popup {
        position: fixed; top: 20px; right: 20px; z-index: 9999;
        min-width: 300px; max-width: 450px; padding: 16px 20px;
        border-radius: 10px; font-weight: 600;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        display: flex; align-items: center; justify-content: space-between;
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

    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(15, 23, 42, 0.6); display: flex; justify-content: center; align-items: center; opacity: 0; pointer-events: none; transition: all 0.25s ease; z-index: 999; }
    .modal-overlay.show { opacity: 1; pointer-events: auto; }
    .modal-box { background-color: #fff; padding: 30px; border-radius: 12px; width: 460px; max-width: 90%; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); transform: scale(0.9); transition: all 0.25s ease; box-sizing: border-box; }
    .modal-overlay.show .modal-box { transform: scale(1); }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; }
    .btn-close { background: none; border: none; font-size: 28px; cursor: pointer; color: #94a3b8; }

    .form-group { margin-bottom: 16px; }
    .form-group label { display: block; font-size: 14px; font-weight: 600; margin-bottom: 6px; color: #334155; }
    .form-group input, .form-group select { width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; background-color: #fff; box-sizing: border-box; }
    .form-group input[readonly] { background-color: #f1f5f9; color: #64748b; cursor: not-allowed; }

    .btn-simpan {
        width: 100%; color: white; border: none;
        padding: 12px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 15px;
        transition: opacity 0.2s;
    }
    .btn-simpan.loading { pointer-events: none; opacity: 0.8; }

    /* Skeleton Loading Style */
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

    .pagination-btn:hover {
        border-color: #3b82f6 !important;
        color: #2563eb !important;
        background-color: #f8fafc !important;
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

    @if(session('eror') || $errors->has('nama_pelatih'))
        <div id="notifAlert" class="notif-popup notif-gagal">
            <span>{{ session('eror') ?? $errors->first('nama_pelatih') }}</span>
            <button class="notif-close" onclick="tutupNotif()">&times;</button>
        </div>
    @endif

    <!-- TABEL 1: DATA PELATIH -->
    <div class="top-bar">
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            @if(Auth::user()->isAdmin())
                <button class="btn-tambah" onclick="bukaModalPelatih()">[+] TAMBAH DATA PELATIH</button>
            @endif
        </div>
        <div class="filter-box">
            <input type="text" id="inputCariPelatih" class="input-filter" placeholder="Cari nama pelatih..." value="{{ request('cari_pelatih') }}" autocomplete="off">
        </div>
    </div>

    <div class="table-container" id="wrapperTabelPelatih">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pelatih</th>
                    <th>No. HP / WA</th>
                    <th>Tarif Per Sesi/Hari</th>
                    <th>Status Hadir</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="skeletonBodyPelatih" style="display: none;">
                @for ($i = 0; $i < 5; $i++)
                <tr>
                    <td><div class="skeleton" style="width: 20px; height: 16px;"></div></td>
                    <td><div class="skeleton" style="width: 120px; height: 16px;"></div></td>
                    <td><div class="skeleton" style="width: 90px; height: 16px;"></div></td>
                    <td><div class="skeleton" style="width: 80px; height: 16px;"></div></td>
                    <td><div class="skeleton" style="width: 100px; height: 20px;"></div></td>
                    <td><div class="skeleton" style="width: 80px; height: 24px;"></div></td>
                </tr>
                @endfor
            </tbody>
            <tbody id="tabelBodyPelatih">
                @forelse($daftarPelatih as $index => $row)
                <tr>
                    <td>{{ ($daftarPelatih->currentPage() - 1) * $daftarPelatih->perPage() + $index + 1 }}</td>
                    <td><strong>{{ $row->nama_pelatih }}</strong></td>
                    <td>{{ $row->nomor_telepon }}</td>
                    <td><span style="font-weight: 600; color: #0284c7;">Rp {{ number_format($row->tarif_harian, 0, ',', '.') }}</span></td>
                    <td>
                        <span class="badge {{ $row->status_hadir == 'hadir' ? 'badge-hadir' : 'badge-absen' }}">
                            {{ $row->status_hadir == 'hadir' ? 'Hadir / Melatih' : 'Tidak Hadir' }}
                        </span>
                    </td>
                    <td>
                        <button style="background-color: #f59e0b; color: white; border: none; padding: 6px 10px; border-radius: 6px; cursor: pointer; font-size: 11px; font-weight: 600;" data-pelatih="{{ json_encode($row) }}" onclick="bukaModalEditPelatih(JSON.parse(this.getAttribute('data-pelatih')))">Edit/Absen</button>
                        @if(Auth::user()->isAdmin())
                        <form action="{{ route('pelatih.destroy', $row->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus data pelatih ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="background-color: #ef4444; color: white; border: none; padding: 6px 10px; border-radius: 6px; cursor: pointer; font-size: 11px; font-weight: 600;">Hapus</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align: center; color: #94a3b8; padding: 20px;">Belum ada data pelatih.</td></tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination Pelatih -->
        <div style="margin-top: 20px;" id="paginationPelatihWrapper">
            {{ $daftarPelatih->appends(request()->query())->links('vendor.pagination.custom') }}
        </div>
    </div>
    

    <!-- TABEL 2: CATATAN SEWA PELATIH (PENGGUNA) -->
    @if(Auth::user()->isKasir())
    <div class="top-bar" style="margin-top: 35px;">
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <button class="btn-tambah-user" onclick="bukaModalPengguna()">[+] DAFTARKAN SEWA PELATIH</button>
        </div>
        <div class="filter-box">
            <input type="text" id="inputCariSewa" class="input-filter" placeholder="Cari nama member sewa..." value="{{ request('cari_sewa') }}" autocomplete="off">
        </div>
    </div>

    <div class="table-container" id="wrapperTabelSewa">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Member</th>
                    <th>No. HP Member</th>
                    <th>Nama Pelatih (PT)</th>
                    <th>Biaya Sewa</th>
                    <th>Tanggal & Waktu Input</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="skeletonBodySewa" style="display: none;">
                @for ($i = 0; $i < 5; $i++)
                <tr>
                    <td><div class="skeleton" style="width: 20px; height: 16px;"></div></td>
                    <td><div class="skeleton" style="width: 120px; height: 16px;"></div></td>
                    <td><div class="skeleton" style="width: 90px; height: 16px;"></div></td>
                    <td><div class="skeleton" style="width: 100px; height: 16px;"></div></td>
                    <td><div class="skeleton" style="width: 80px; height: 16px;"></div></td>
                    <td><div class="skeleton" style="width: 110px; height: 20px;"></div></td>
                    <td><div class="skeleton" style="width: 60px; height: 24px;"></div></td>
                </tr>
                @endfor
            </tbody>
            <tbody id="tabelBodySewa">
                @forelse($daftarPengguna as $index => $user)
                <tr>
                    <td>{{ ($daftarPengguna->currentPage() - 1) * $daftarPengguna->perPage() + $index + 1 }}</td>
                    <td><strong>{{ $user->nama_pengguna }}</strong></td>
                    <td>{{ $user->nomor_telepon_pengguna }}</td>
                    <td>{{ $user->pelatih->nama_pelatih ?? 'Pelatih Dihapus' }}</td>
                    <td><span style="font-weight: 600; color: #10b981;">Rp {{ number_format($user->tarif_jasa, 0, ',', '.') }}</span></td>
                    <td><span class="badge badge-waktu">{{ $user->created_at->format('d/m/Y H:i') }} WIB</span></td>
                    <td>
                        <form action="{{ route('pelatih.destroyPengguna', $user->id) }}" method="POST" onsubmit="return confirm('Hapus riwayat sewa member ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="background-color: #ef4444; color: white; border: none; padding: 6px 10px; border-radius: 6px; cursor: pointer; font-size: 11px; font-weight: 600;">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align: center; color: #94a3b8; padding: 20px;">Belum ada member yang menyewa pelatih hari ini.</td></tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination Sewa -->
        <div style="margin-top: 20px;" id="paginationSewaWrapper">
            {{ $daftarPengguna->appends(request()->query())->links('vendor.pagination.custom') }}
        </div>
    </div>
    @endif


    <!-- MODAL PELATIH -->
     
    <div class="modal-overlay" id="modalPelatih">
        <div class="modal-box">
            <div class="modal-header">
                <h2 id="titlePelatih">Tambah Data Pelatih</h2>
                <button class="btn-close" onclick="tutupModal('modalPelatih')">&times;</button>
            </div>
            <form id="formPelatih" action="" method="POST">
                @csrf <div id="methodFieldPelatih"></div>
                
                <input type="hidden" name="tarif_bulanan" id="tarif_bulanan" min="0" value="0">

                <div class="form-group">
                    <label>Nama Lengkap Pelatih:</label>
                    <input type="text" name="nama_pelatih" id="nama_pelatih" required placeholder="Masukkan nama pelatih...">
                </div>
                <div class="form-group">
                    <label>No. HP / WhatsApp:</label>
                    <input type="text" name="nomor_telepon" id="nomor_telepon" required placeholder="08xxxxxxxx" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                </div>
               
                
                <div class="form-group">
                    <label>Tarif Per Sesi/Hari (Rp):</label>
                    <input type="number" name="tarif_harian" id="tarif_harian" min="0" value="20000" required>
                </div>
                <div class="form-group">
                    <label>Status Kehadiran Hari Ini:</label>
                    <select name="status_hadir" id="status_hadir">
                        <option value="hadir">Hadir & Bisa Melatih</option>
                        <option value="tidak_hadir">Tidak Hadir / Libur</option>
                    </select>
                </div>
                <button type="submit" class="btn-simpan" id="btnSubmitPelatih" style="background-color:#38bdf8;">SIMPAN DATA</button>
            </form>
        </div>
    </div>

    <!-- MODAL SEWA PENGGUNA PELATIH -->
    <div class="modal-overlay" id="modalPengguna">
        <div class="modal-box">
            <div class="modal-header">
                <h2>Daftarkan Sewa Pelatih (Harian)</h2>
                <button class="btn-close" onclick="tutupModal('modalPengguna')">&times;</button>
            </div>
            <form id="formPengguna" action="{{ route('pelatih.storePengguna') }}" method="POST">
                @csrf
                <input type="hidden" name="tipe_jasa" value="perhari">

                <div class="form-group">
                    <label>Nama Pengguna:</label>
                    <input type="text" name="nama_pengguna" required placeholder="Nama member...">
                </div>
                <div class="form-group">
                    <label>No. HP Pengguna:</label>
                    <input type="text" name="nomor_telepon_pengguna" required placeholder="08xxxx" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                </div>
                <div class="form-group">
                    <label>Pilih Pelatih:</label>
                    <select name="pelatih_id" id="selectPelatih" class="form-control" required>
                        <option value="">-- Pilih Pelatih --</option>
                        @foreach($daftarPelatih as $p)
                            @if($p->status_hadir == 'hadir')
                                <option value="{{ $p->id }}" data-harian="{{ $p->tarif_harian }}">{{ $p->nama_pelatih }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Tarif Jasa Per Sesi:</label>
                    <input type="number" name="tarif_jasa" id="tarifTerkunci" readonly required>
                </div>
                <button type="submit" class="btn-simpan" id="btnSubmitPengguna" style="background-color:#10b981;">PROSES SEWA</button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<!-- Choices.js JS -->
<script src="{{ asset('js/choices.min.js') }}"></script>
<script>
    // Inisialisasi Choices.js untuk Dropdown Pelatih
    let choicesPelatih = null;
    
    document.addEventListener("DOMContentLoaded", function() {
        const selectPelatihEl = document.getElementById('selectPelatih');
        if (selectPelatihEl) {
            choicesPelatih = new Choices(selectPelatihEl, {
                searchEnabled: true,
                itemSelectText: '',
                shouldSort: false,
                position: 'bottom',
                noResultsText: 'Pelatih tidak ditemukan',
                noChoicesText: 'Tidak ada pelatih yang hadir'
            });
        }
    });

    // Tangkap perubahan nilai pada Choices.js untuk otomatis mengisi tarif harian
    document.getElementById('selectPelatih').addEventListener('change', function(event) {
        const selectedOption = this.options[this.selectedIndex];
        const inputTarif = document.getElementById('tarifTerkunci');
        if (selectedOption && selectedOption.getAttribute('data-harian')) {
            inputTarif.value = selectedOption.getAttribute('data-harian');
        } else {
            inputTarif.value = '';
        }
    });
    
    // Pastikan instance choices di-update atau di-reset saat modal dibuka jika diperlukan
    function bukaModalPengguna() { 
        document.getElementById('modalPengguna').classList.add('show'); 
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
            setTimeout(() => { tutupNotif(); }, 6000);
        }
        attachPelatihEvents();
        attachSewaEvents();
    });

    function bukaModalPelatih() {
        document.getElementById('titlePelatih').innerText = "Tambah Pelatih Baru";
        document.getElementById('formPelatih').action = "{{ route('pelatih.store') }}";
        document.getElementById('methodFieldPelatih').innerHTML = "";
        document.getElementById('nama_pelatih').value = "";
        document.getElementById('nama_pelatih').readOnly = false;
        document.getElementById('nomor_telepon').value = "";
        document.getElementById('nomor_telepon').readOnly = false;
        document.getElementById('tarif_harian').value = 20000;
        document.getElementById('tarif_harian').readOnly = false;
        document.getElementById('status_hadir').value = "hadir";
        document.getElementById('btnSubmitPelatih').innerText = "SIMPAN DATA";
        document.getElementById('modalPelatih').classList.add('show');
    }

    function bukaModalEditPelatih(data) {
        document.getElementById('titlePelatih').innerText = "Edit & Absen Pelatih";
        
        @if(Auth::check() && Auth::user()->isKasir())
            document.getElementById('formPelatih').action = "/pelatih/" + data.id + "/absen";
            document.getElementById('methodFieldPelatih').innerHTML = `@method('PATCH')`;
            document.getElementById('nama_pelatih').value = data.nama_pelatih;
            document.getElementById('nama_pelatih').readOnly = true;
            document.getElementById('nomor_telepon').value = data.nomor_telepon;
            document.getElementById('nomor_telepon').readOnly = true;
            document.getElementById('tarif_harian').value = data.tarif_harian;
            document.getElementById('tarif_harian').readOnly = true;
            document.getElementById('btnSubmitPelatih').innerText = "PERBARUI STATUS";
        @else
            document.getElementById('formPelatih').action = "/pelatih/" + data.id;
            document.getElementById('methodFieldPelatih').innerHTML = `@method('PUT')`;
            document.getElementById('nama_pelatih').value = data.nama_pelatih;
            document.getElementById('nama_pelatih').readOnly = false;
            document.getElementById('nomor_telepon').value = data.nomor_telepon;
            document.getElementById('nomor_telepon').readOnly = false;
            document.getElementById('tarif_harian').value = data.tarif_harian;
            document.getElementById('tarif_harian').readOnly = false;
            document.getElementById('btnSubmitPelatih').innerText = "PERBARUI DATA";
        @endif

        document.getElementById('status_hadir').value = data.status_hadir;
        document.getElementById('modalPelatih').classList.add('show');
    }

    function bukaModalPengguna() { document.getElementById('modalPengguna').classList.add('show'); }
    function tutupModal(id) { document.getElementById(id).classList.remove('show'); }

    function hitungTarifOtomatis() {
        const selectPelatih = document.getElementById('selectPelatih');
        const inputTarif = document.getElementById('tarifTerkunci');
        const pilihanTerpilih = selectPelatih.options[selectPelatih.selectedIndex];
        
        if (!pilihanTerpilih.value) { inputTarif.value = ""; return; }

        const hargaHarian = pilihanTerpilih.getAttribute('data-harian');
        inputTarif.value = hargaHarian;
    }

    const formPelatih = document.getElementById('formPelatih');
    const btnSubmitPelatih = document.getElementById('btnSubmitPelatih');
    if (formPelatih) {
        formPelatih.addEventListener('submit', function() {
            if (btnSubmitPelatih) {
                btnSubmitPelatih.classList.add("loading");
                btnSubmitPelatih.innerHTML = `
                    <svg width="18" height="18" viewBox="0 0 50 50" style="vertical-align: middle; margin-right: 6px;">
                        <circle cx="25" cy="25" r="20" fill="none" stroke="white" stroke-width="5" stroke-linecap="round" stroke-dasharray="31.4 31.4">
                            <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="0.8s" values="0 25 25;360 25 25"/>
                        </circle>
                    </svg>
                    Memproses...
                `;
            }
        });
    }

    const formPengguna = document.getElementById('formPengguna');
    const btnSubmitPengguna = document.getElementById('btnSubmitPengguna');
    if (formPengguna) {
        formPengguna.addEventListener('submit', function() {
            if (btnSubmitPengguna) {
                btnSubmitPengguna.classList.add("loading");
                btnSubmitPengguna.innerHTML = `
                    <svg width="18" height="18" viewBox="0 0 50 50" style="vertical-align: middle; margin-right: 6px;">
                        <circle cx="25" cy="25" r="20" fill="none" stroke="white" stroke-width="5" stroke-linecap="round" stroke-dasharray="31.4 31.4">
                            <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="0.8s" values="0 25 25;360 25 25"/>
                        </circle>
                    </svg>
                    Memproses...
                `;
            }
        });
    }

    function fetchPelatihData(url) {
        const tbody = document.getElementById('tabelBodyPelatih');
        const skeleton = document.getElementById('skeletonBodyPelatih');
        if (tbody && skeleton) {
            tbody.style.display = 'none';
            skeleton.style.display = 'table-row-group';
        }

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newWrapper = doc.getElementById('wrapperTabelPelatih');
                if (newWrapper) {
                    document.getElementById('wrapperTabelPelatih').innerHTML = newWrapper.innerHTML;
                    attachPelatihEvents();
                }
            })
            .catch(err => console.error('Gagal memuat data pelatih:', err));
    }

    function fetchSewaData(url) {
        const tbody = document.getElementById('tabelBodySewa');
        const skeleton = document.getElementById('skeletonBodySewa');
        if (tbody && skeleton) {
            tbody.style.display = 'none';
            skeleton.style.display = 'table-row-group';
        }

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newWrapper = doc.getElementById('wrapperTabelSewa');
                if (newWrapper) {
                    document.getElementById('wrapperTabelSewa').innerHTML = newWrapper.innerHTML;
                    attachSewaEvents();
                }
            })
            .catch(err => console.error('Gagal memuat data sewa:', err));
    }

    function attachPelatihEvents() {
        const inputCari = document.getElementById('inputCariPelatih');
        let searchTimer;
        if (inputCari) {
            inputCari.addEventListener('input', function() {
                clearTimeout(searchTimer);
                const keyword = this.value;
                searchTimer = setTimeout(() => {
                    const url = `{{ route('pelatih.index') }}?cari_pelatih=${encodeURIComponent(keyword)}`;
                    fetchPelatihData(url);
                }, 300);
            });
        }

        document.querySelectorAll('#wrapperTabelPelatih .pagination-btn, #wrapperTabelPelatih a.page-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                fetchPelatihData(this.href);
            });
        });
    }

    function attachSewaEvents() {
        const inputCariSewa = document.getElementById('inputCariSewa');
        let searchTimerSewa;
        if (inputCariSewa) {
            inputCariSewa.addEventListener('input', function() {
                clearTimeout(searchTimerSewa);
                const keyword = this.value;
                searchTimerSewa = setTimeout(() => {
                    const url = `{{ route('pelatih.index') }}?cari_sewa=${encodeURIComponent(keyword)}`;
                    fetchSewaData(url);
                }, 300);
            });
        }

        document.querySelectorAll('#wrapperTabelSewa .pagination-btn, #wrapperTabelSewa a.page-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                fetchSewaData(this.href);
            });
        });
    }

    window.addEventListener('click', function(event) {
        const modalPelatih = document.getElementById('modalPelatih');
        const modalPengguna = document.getElementById('modalPengguna');
        if (event.target === modalPelatih) { tutupModal('modalPelatih'); }
        if (event.target === modalPengguna) { tutupModal('modalPengguna'); }
    });

    window.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            tutupModal('modalPelatih');
            tutupModal('modalPengguna');
        }
    });
</script>
@endpush