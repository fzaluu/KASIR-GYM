@extends('layouts.app')

@section('title', 'Kunjungan Harian - Virgo Gym')
@section('page_title', 'Data Pengunjung Harian')

@push('styles')
<style>
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

    .top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
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
        width: 100%; background-color: #38bdf8; color: white; border: none;
        padding: 12px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 15px;
    }
    .btn-simpan:hover { background-color: #0ea5e9; }

    @media screen and (max-width: 768px) {
        .top-bar { flex-direction: column; align-items: stretch; }
        .filter-box form { width: 100%; }
        .input-filter { flex: 1; min-width: 120px; }
        .btn-filter { width: 100%; text-align: center; }
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

    <div class="top-bar">
        <button class="btn-tambah" onclick="bukaModalTambah()">[+] INPUT PENGUNJUNG HARIAN</button>
        
        <div class="filter-box">
            <form action="{{ route('harian.index') }}" method="GET" onsubmit="return false;">
                <input type="text" id="inputCariHarian" name="cari" class="input-filter" placeholder="Cari nama..." value="{{ request('cari') }}">
                <input type="date" name="tanggal" class="input-filter" value="{{ request('tanggal', date('Y-m-d')) }}" onchange="this.form.submit()">
            </form>
        </div>
    </div>

    <div class="table-container">
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
            <tbody id="tabelBodyHarian">
                @forelse($daftarHarian as $index => $row)
                <tr class="baris-harian">
                    <td>{{ $index + 1 }}</td>
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
                        Tidak ada pengunjung harian pada tanggal yang dipilih.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
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
                    <input type="number" name="nominal" id="inputNominal" min="0" value="8000" readonly>
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
    const btnSubmit = document.getElementById('btnSubmitForm');

    const inputNama = document.getElementById('inputNama');
    const inputNominal = document.getElementById('inputNominal');

    const inputCari = document.getElementById('inputCariHarian');
    const tabelBody = document.getElementById('tabelBodyHarian');

    inputCari.addEventListener('keyup', function() {
        const keyword = inputCari.value.toLowerCase();
        const rows = tabelBody.getElementsByClassName('baris-harian');
        let ditemukan = false;

        for (let i = 0; i < rows.length; i++) {
            const cellNama = rows[i].getElementsByClassName('nama-target')[0];
            if (cellNama) {
                const teksNama = cellNama.textContent || cellNama.innerText;
                if (teksNama.toLowerCase().indexOf(keyword) > -1) {
                    rows[i].style.display = "";
                    ditemukan = true;
                } else {
                    rows[i].style.display = "none";
                }
            }
        }

        const pesanLama = document.getElementById('pesanKosongCari');
        if (pesanLama) pesanLama.remove();

        const bawaanKosong = document.getElementById('barisKosongBawaan');
        if (bawaanKosong) {
            if (keyword !== '') {
                bawaanKosong.style.display = "none";
            } else if (rows.length === 0) {
                bawaanKosong.style.display = "";
                ditemukan = true;
            }
        }

        if (!ditemukan && rows.length > 0) {
            const tr = document.createElement('tr');
            tr.id = 'pesanKosongCari';
            tr.innerHTML = `<td colspan="5" style="text-align: center; color: #94a3b8; padding: 20px;">Tidak ada pengunjung harian dengan kata kunci "${inputCari.value}"</td>`;
            tabelBody.appendChild(tr);
        }
    });

    function bukaModalTambah() {
        modalTitle.innerText = "Input Pengunjung Harian Baru";
        form.action = "{{ route('harian.store') }}";
        methodField.innerHTML = "";
        
        inputNama.value = "";
        inputNominal.value = "8000";
        btnSubmit.innerText = "INPUT KUNJUNGAN";
        
        modal.classList.add('show');
    }

    function bukaModalEdit(data) {
    modalTitle.innerText = "Edit Data Pengunjung";
    form.action = "/harian/" + data.id;
    methodField.innerHTML = `@method('PUT')`;
    
    inputNama.value = data.nama_pelanggan;
    
    // 🛡️ ANTI-MINUS FRONTEND: Jika data lama minus, otomatis reset ke 0 atau 8000 saat di-edit
    if (data.nominal < 0) {
        inputNominal.value = "8000"; 
    } else {
        inputNominal.value = data.nominal;
    }
    
    btnSubmit.innerText = "PERBARUI DATA";
    
    modal.classList.add('show');
    }

    function tutupModal() {
        modal.classList.remove('show');
    }
</script>
@endpush