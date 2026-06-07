@extends('layouts.app')

@section('title', 'Data Pelatih - Virgo Gym')
@section('page_title', 'Manajemen Data Pelatih / Trainer')

@push('styles')
<style>
    /* 🔍 TOP BAR: TOMBOL & FILTER CARI */
    .top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
    }

    .btn-tambah {
        background-color: #38bdf8;
        color: #fff;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        font-size: 14px;
        cursor: pointer;
        font-weight: 600;
        transition: background-color 0.2s;
        box-shadow: 0 4px 6px -1px rgba(56, 189, 248, 0.2);
    }
    .btn-tambah:hover { background-color: #0ea5e9; }

    /* Kotak Cari Nama Pelatih */
    .filter-box form {
        display: flex;
        gap: 8px;
    }

    .input-filter {
        padding: 10px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        background-color: #fff;
        width: 220px;
    }
    .input-filter:focus { border-color: #38bdf8; }

    .btn-filter {
        background-color: #475569;
        color: white;
        border: none;
        padding: 10px 16px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 14px;
    }
    .btn-filter:hover { background-color: #334155; }

    /* 📋 TABLE STYLE */
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
    }
    .btn-hapus:hover { background-color: #dc2626; }

    /* 🏛️ MODAL STYLE */
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
    .form-group input { width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; }
    .form-group input:focus { border-color: #38bdf8; }

    .btn-simpan {
        width: 100%; background-color: #10b981; color: white; border: none;
        padding: 12px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 15px;
    }
    .btn-simpan:hover { background-color: #059669; }

    @media screen and (max-width: 768px) {
        .top-bar { flex-direction: column; align-items: stretch; }
        .filter-box form { width: 100%; }
        .input-filter { flex: 1; }
        .btn-filter { width: auto; }
        .table-container { padding: 12px; overflow-x: auto; }
        table { min-width: 550px; }
    }
</style>
@endpush

@section('konten')

    @if(session('sukses'))
        <div style="background-color: #d1fae5; color: #065f46; padding: 16px; border-radius: 8px; font-weight: 600; border: 1px solid #a7f3d0; margin-bottom: 15px;">
            {{ session('sukses') }}
        </div>
    @endif

    <div class="top-bar">
        <button class="btn-tambah" onclick="bukaModalTambah()">[+] TAMBAH DATA PELATIH</button>
        
        <div class="filter-box">
            <form action="{{ route('pelatih.index') }}" method="GET">
                <input type="text" name="cari" class="input-filter" placeholder="Cari nama pelatih..." value="{{ request('cari') }}">
                <button type="submit" class="btn-filter">Cari</button>
            </form>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pelatih</th>
                    <th>No. Telepon / WA</th>
                    <th>Tarif / Bulan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($daftarPelatih as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $row->nama_pelatih }}</strong></td>
                    <td>{{ $row->nomor_telepon }}</td>
                    <td><span style="color: #475569; font-weight: 600;">Rp {{ number_format($row->tarif_per_bulan, 0, ',', '.') }}</span></td>
                    <td>
                        <div class="action-btns">
                           <button class="btn-edit" data-pelatih="{{ json_encode($row) }}" onclick="bukaModalEdit(JSON.parse(this.getAttribute('data-pelatih')))">Edit</button>
                            
                            <form action="{{ route('pelatih.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Hapus data pelatih ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-hapus">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #94a3b8; padding: 20px;">
                        Data pelatih tidak ditemukan atau belum diinput.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="modal-overlay" id="modalPelatih">
        <div class="modal-box">
            <div class="modal-header">
                <h2 id="modalTitle">Tambah Data Pelatih</h2>
                <button class="btn-close" onclick="tutupModal()">&times;</button>
            </div>
            
            <form id="formPelatih" action="" method="POST">
                @csrf
                <div id="methodField"></div>
                
                <div class="form-group">
                    <label>Nama Lengkap Pelatih:</label>
                    <input type="text" name="nama_pelatih" id="inputNama" placeholder="Nama lengkap trainer..." required>
                </div>

                <div class="form-group">
                    <label>No. HP / WhatsApp:</label>
                    <input type="text" name="nomor_telepon" id="inputTelepon" placeholder="Contoh: 08123xxxx" required>
                </div>

                <div class="form-group">
                    <label>Tarif Jasa per Bulan (Rp):</label>
                    <input type="number" name="tarif_per_bulan" id="inputTarif" placeholder="Masukkan tarif bulanan..." required>
                </div>

                <button type="submit" class="btn-simpan" id="btnSubmitForm">SIMPAN DATA</button>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    const modal = document.getElementById('modalPelatih');
    const form = document.getElementById('formPelatih');
    const modalTitle = document.getElementById('modalTitle');
    const methodField = document.getElementById('methodField');
    const btnSubmit = document.getElementById('btnSubmitForm');

    const inputNama = document.getElementById('inputNama');
    const inputTelepon = document.getElementById('inputTelepon');
    const inputTarif = document.getElementById('inputTarif');

    function bukaModalTambah() {
        modalTitle.innerText = "Tambah Pelatih Baru Baru";
        form.action = "{{ route('pelatih.store') }}"; // Menembak ke rute store bawaan resource pelatih
        methodField.innerHTML = "";
        
        inputNama.value = "";
        inputTelepon.value = "";
        inputTarif.value = "";
        btnSubmit.innerText = "TAMBAH PELATIH";
        
        modal.classList.add('show');
    }

    function bukaModalEdit(data) {
        modalTitle.innerText = "Edit Data Pelatih";
        form.action = "/pelatih/" + data.id; // Menembak ke rute update resource standar (/pelatih/{id})
        methodField.innerHTML = `@method('PUT')`;
        
        inputNama.value = data.nama_pelatih;
        inputTelepon.value = data.nomor_telepon;
        inputTarif.value = data.tarif_per_bulan;
        btnSubmit.innerText = "PERBARUI DATA";
        
        modal.classList.add('show');
    }

    function tutupModal() {
        modal.classList.remove('show');
    }
</script>
@endpush