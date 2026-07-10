@extends('layouts.app')

@section('title', 'Manajemen Pelatih & Pengguna - Virgo Gym')
@section('page_title', 'Manajemen Pelatih & Pengguna Jasa (PT)')

@push('styles')
<style>
    .top-bar { display: flex; justify-content: flex-start; align-items: center; gap: 15px; flex-wrap: wrap; margin-bottom: 20px; }
    
    /* 🏛️ PERBAIKAN UI: Mengatur posisi judul dan tombol di bawahnya (Rata Kiri) */
    .section-block { margin: 30px 0 15px 0; display: flex; flex-direction: column; gap: 8px; align-items: flex-start; }
    .section-title { font-size: 18px; font-weight: 700; color: #1e293b; }
    
    .btn-tambah { background-color: #38bdf8; color: #fff; border: none; padding: 10px 16px; border-radius: 8px; font-size: 13px; cursor: pointer; font-weight: 600; box-shadow: 0 4px 6px -1px rgba(56, 189, 248, 0.2); }
    .btn-tambah:hover { background-color: #0ea5e9; }
    .btn-tambah-user { background-color: #10b981; color: #fff; border: none; padding: 10px 16px; border-radius: 8px; font-size: 13px; cursor: pointer; font-weight: 600; }
    .btn-tambah-user:hover { background-color: #059669; }
    
    .table-container { background-color: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; margin-bottom: 20px; }
    table { width: 100%; border-collapse: collapse; text-align: left; }
    th, td { padding: 12px 14px; border-bottom: 1px solid #e2e8f0; font-size: 14px; }
    th { background-color: #f8fafc; color: #475569; font-weight: 600; }
    tr:hover td { background-color: #f8fafc; }

    .badge { padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
    .badge-hadir { background-color: #d1fae5; color: #065f46; }
    .badge-absen { background-color: #fee2e2; color: #991b1b; }
    .badge-waktu { background-color: #f1f5f9; color: #475569; font-weight: normal; }

    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(15, 23, 42, 0.6); display: flex; justify-content: center; align-items: center; opacity: 0; pointer-events: none; transition: all 0.25s ease; z-index: 999; }
    .modal-overlay.show { opacity: 1; pointer-events: auto; }
    .modal-box { background-color: #fff; padding: 25px; border-radius: 12px; width: 450px; max-width: 90%; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); transform: scale(0.9); transition: all 0.25s ease; box-sizing: border-box; }
    .modal-overlay.show .modal-box { transform: scale(1); }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px; }
    .btn-close { background: none; border: none; font-size: 24px; cursor: pointer; color: #94a3b8; }

    .form-group { margin-bottom: 14px; }
    .form-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px; color: #334155; }
    .form-group input, .form-group select { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; background-color: #fff; box-sizing: border-box; }
    .form-group input[readonly] { background-color: #f1f5f9; color: #64748b; cursor: not-allowed; }

    /* 📱 RESPONSIVE MEDIA QUERIES KASIR MOBILE */
    @media screen and (max-width: 768px) {
        .table-container {
            padding: 12px;
            overflow-x: auto; 
            -webkit-overflow-scrolling: touch;
        }
        table {
            min-width: 650px; /* Memaksa tabel bisa di-scroll smooth ke samping di HP */
        }
        th, td {
            padding: 10px 12px;
            font-size: 13px;
        }
        .section-block {
            align-items: stretch; /* Tombol tambah melebar penuh di layar HP */
        }
        .btn-tambah, .btn-tambah-user {
            text-align: center;
            width: 100%;
        }
    }
</style>
@endpush

@section('konten')

    @if(session('sukses'))
        <div style="background-color: #d1fae5; color: #065f46; padding: 16px; border-radius: 8px; font-weight: 600; border: 1px solid #a7f3d0; margin-bottom: 15px;">
            {{ session('sukses') }}
        </div>
    @endif
   @if($errors->has('nama_pelatih'))
    <div style="background-color: #fee2e2; color: #991b1b; padding: 16px; border-radius: 8px; font-weight: 600; border: 1px solid #fca5a5; margin-bottom: 15px;">
        {{ $errors->first('nama_pelatih') }}
    </div>
@endif

    <div class="section-block">
        <span class="section-title">🏋️ Daftar Master Pelatih & Absensi</span>
        <button class="btn-tambah" onclick="bukaModalPelatih()">[+] TAMBAH DATA PELATIH</button>
    </div>
    
    <div class="table-container">
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
            <tbody>
                @forelse($daftarPelatih as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $row->nama_pelatih }}</strong></td>
                    <td>{{ $row->nomor_telepon }}</td>
                    <td><span style="font-weight: 600; color: #0284c7;">Rp {{ number_format($row->tarif_harian, 0, ',', '.') }}</span></td>
                    <td>
                        <span class="badge {{ $row->status_hadir == 'hadir' ? 'badge-hadir' : 'badge-absen' }}">
                            {{ $row->status_hadir == 'hadir' ? 'Hadir / Melatih' : 'Tidak Hadir' }}
                        </span>
                    </td>
                    <td>
                        <button style="background-color: #f59e0b; color: white; border: none; padding: 5px 10px; border-radius: 6px; cursor: pointer; font-size: 12px;" data-pelatih="{{ json_encode($row) }}" onclick="bukaModalEditPelatih(JSON.parse(this.getAttribute('data-pelatih')))">Edit/Absen</button>
                        <form action="{{ route('pelatih.destroy', $row->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus data pelatih ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="background-color: #ef4444; color: white; border: none; padding: 5px 10px; border-radius: 6px; cursor: pointer; font-size: 12px;">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align: center; color: #94a3b8;">Belum ada data pelatih.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section-block">
        <span class="section-title">👥 Catatan Member Sewa Jasa Pelatih (PT)</span>
        <button class="btn-tambah-user" onclick="bukaModalPengguna()">[+] DAFTARKAN SEWA PELATIH</button>
    </div>

    <div class="table-container">
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
            <tbody>
                @forelse($daftarPengguna as $index => $user)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $user->nama_pengguna }}</strong></td>
                    <td>{{ $user->nomor_telepon_pengguna }}</td>
                    <td>{{ $user->pelatih->nama_pelatih ?? 'Pelatih Dihapus' }}</td>
                    <td><span style="font-weight: 600; color: #10b981;">Rp {{ number_format($user->tarif_jasa, 0, ',', '.') }}</span></td>
                    <td><span class="badge badge-waktu">{{ $user->created_at->format('d/m/Y H:i') }} WIB</span></td>
                    <td>
                        <form action="{{ route('pelatih.destroyPengguna', $user->id) }}" method="POST" onsubmit="return confirm('Hapus riwayat sewa member ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="background-color: #ef4444; color: white; border: none; padding: 5px 10px; border-radius: 6px; cursor: pointer; font-size: 12px;">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align: center; color: #94a3b8;">Belum ada member yang menyewa pelatih hari ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="modal-overlay" id="modalPelatih">
        <div class="modal-box">
            <div class="modal-header">
                <h2 id="titlePelatih">Tambah Data Pelatih</h2>
                <button class="btn-close" onclick="tutupModal('modalPelatih')">&times;</button>
            </div>
            <form id="formPelatih" action="" method="POST">
                @csrf <div id="methodFieldPelatih"></div>
                
                <input type="hidden" name="tarif_bulanan" id="tarif_bulanan" value="0">

                <div class="form-group">
                    <label>Nama Lengkap Pelatih:</label>
                    <input type="text" name="nama_pelatih" id="nama_pelatih" required placeholder="Masukkan nama trainer...">
                </div>
                <div class="form-group">
                    <label>No. HP / WhatsApp:</label>
                    <input type="text" name="nomor_telepon" id="nomor_telepon" required placeholder="08xxxxxxxx" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                </div>
                <div class="form-group">
                    <label>Tarif Per Sesi/Hari (Rp):</label>
                    <input type="number" name="tarif_harian" id="tarif_harian" value="20000" required>
                </div>
                <div class="form-group">
                    <label>Status Kehadiran Hari Ini:</label>
                    <select name="status_hadir" id="status_hadir">
                        <option value="hadir">Hadir & Bisa Melatih</option>
                        <option value="tidak_hadir">Tidak Hadir / Libur</option>
                    </select>
                </div>
                <button type="submit" style="width:100%; padding:12px; border-radius:8px; border:none; background-color:#38bdf8; color:white; font-weight:600; cursor:pointer;" id="btnSubmitPelatih">SIMPAN</button>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="modalPengguna">
        <div class="modal-box">
            <div class="modal-header">
                <h2>Daftarkan Sewa Pelatih (Harian)</h2>
                <button class="btn-close" onclick="tutupModal('modalPengguna')">&times;</button>
            </div>
            <form action="{{ route('pelatih.storePengguna') }}" method="POST">
                @csrf
                
                <input type="hidden" name="tipe_jasa" value="perhari">

                <div class="form-group">
                    <label>Nama Member (Pengguna):</label>
                    <input type="text" name="nama_pengguna" required placeholder="Nama member...">
                </div>
                <div class="form-group">
                    <label>No. HP Member:</label>
                    <input type="text" name="nomor_telepon_pengguna" required placeholder="08xxxx" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                </div>
                <div class="form-group">
                    <label>Pilih Pelatih / Trainer:</label>
                    <select name="pelatih_id" id="selectPelatih" onchange="hitungTarifOtomatis()" required>
                        <option value="">-- Pilih Pelatih --</option>
                        @foreach($daftarPelatih as $p)
                            @if($p->status_hadir == 'hadir')
                                <option value="{{ $p->id }}" data-harian="{{ $p->tarif_harian }}">{{ $p->nama_pelatih }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Tarif Jasa Per Sesi (Terkunci Otomatis):</label>
                    <input type="number" name="tarif_jasa" id="tarifTerkunci" readonly required>
                </div>
                <button type="submit" style="width:100%; padding:12px; border-radius:8px; border:none; background-color:#10b981; color:white; font-weight:600; cursor:pointer;">PROSES SEWA</button>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    function bukaModalPelatih() {
        document.getElementById('titlePelatih').innerText = "Tambah Pelatih Baru";
        document.getElementById('formPelatih').action = "{{ route('pelatih.store') }}";
        document.getElementById('methodFieldPelatih').innerHTML = "";
        document.getElementById('nama_pelatih').value = "";
        document.getElementById('nomor_telepon').value = "";
        document.getElementById('tarif_bulanan').value = 0;
        document.getElementById('tarif_harian').value = 20000;
        document.getElementById('status_hadir').value = "hadir";
        document.getElementById('btnSubmitPelatih').innerText = "SIMPAN DATA";
        document.getElementById('modalPelatih').classList.add('show');
    }

    function bukaModalEditPelatih(data) {
    document.getElementById('titlePelatih').innerText = "Edit & Absen Pelatih";
    document.getElementById('formPelatih').action = "/pelatih/" + data.id;
    document.getElementById('methodFieldPelatih').innerHTML = `@method('PUT')`;
    document.getElementById('nama_pelatih').value = data.nama_pelatih;
    document.getElementById('nomor_telepon').value = data.nomor_telepon;
    document.getElementById('tarif_bulanan').value = 0;
    
    // 🛡️ ANTI-MINUS FRONTEND SINKRONISASI: Menjamin data edit lama tidak membawa minus ke form browser
    if (data.tarif_harian < 0) {
        document.getElementById('tarif_harian').value = 20000;
    } else {
        document.getElementById('tarif_harian').value = data.tarif_harian;
    }
    
    document.getElementById('status_hadir').value = data.status_hadir;
    document.getElementById('btnSubmitPelatih').innerText = "PERBARUI DATA";
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
</script>
@endpush