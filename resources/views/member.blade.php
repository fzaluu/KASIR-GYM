@extends('layouts.app')

@section('title', 'Data Member - Virgo Gym')
@section('page_title', 'Data Member Terdaftar')

@push('styles')
<style>
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

    .badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }
    .badge-aktif { background-color: #d1fae5; color: #065f46; }
    .badge-habis { background-color: #fee2e2; color: #991b1b; }

    .action-btns {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .btn-action {
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 600;
        transition: opacity 0.2s;
    }
    .btn-action:hover { opacity: 0.9; }

    .btn-checkin { background-color: #10b981; }
    .btn-perpanjang { background-color: #38bdf8; }
    .btn-edit { background-color: #f59e0b; }
    .btn-hapus { background-color: #ef4444; }

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
        width: 460px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
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
    .form-group input, .form-group select { width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; background-color: #fff; }
    .form-group input:focus, .form-group select:focus { border-color: #38bdf8; }

    .btn-simpan {
        width: 100%; background-color: #10b981; color: white; border: none;
        padding: 12px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 15px;
    }
    .btn-simpan:hover { background-color: #059669; }

    .info-box-modal {
        background-color: #f8fafc;
        padding: 12px;
        border-radius: 8px;
        border: 1px dashed #cbd5e1;
        font-size: 13px;
        color: #475569;
        margin-bottom: 15px;
        line-height: 1.5;
    }

    @media screen and (max-width: 1024px) {
        .table-container { padding: 12px; overflow-x: auto; }
        table { min-width: 850px; }
    }
</style>
@endpush

@section('konten')

    @if(session('sukses'))
        <div style="background-color: #d1fae5; color: #065f46; padding: 16px; border-radius: 8px; font-weight: 600; border: 1px solid #a7f3d0; margin-bottom: 15px;">
            {{ session('sukses') }}
        </div>
    @endif

    @if(session('eror'))
        <div style="background-color: #fee2e2; color: #991b1b; padding: 16px; border-radius: 8px; font-weight: 600; border: 1px solid #fca5a5; margin-bottom: 15px;">
            {{ session('eror') }}
        </div>
    @endif

    <div class="top-bar">
        <button class="btn-tambah" onclick="bukaModalTambah()">[+] DAFTARKAN MEMBER BARU VIA FORM</button>
        
        <div class="filter-box">
            <form action="{{ route('member.index') }}" method="GET" onsubmit="return false;">
                <input type="text" id="inputCariMember" name="cari" class="input-filter" placeholder="Cari nama member..." value="{{ request('cari') }}">
            </form>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Member</th>
                    <th>No. Telepon</th>
                    <th>Masa Aktif Berakhir</th>
                    <th>Status / Sisa Hari</th>
                    <th>Total Gym (Check-in)</th>
                    <th>Aksi Khusus Kasir</th>
                </tr>
            </thead>
            <tbody id="tabelBodyMember">
                @forelse($daftarMember as $index => $row)
                @php
                    $tanggalSekarang = \Carbon\Carbon::today();
                    $tanggalExpired = \Carbon\Carbon::parse($row->tanggal_kadaluarsa);
                    $sisaHari = $tanggalSekarang->diffInDays($tanggalExpired, false);
                @endphp
                <tr class="baris-member">
                    <td>{{ $index + 1 }}</td>
                    <td class="nama-target"><strong>{{ $row->nama_member }}</strong></td>
                    <td>{{ $row->nomor_telepon }}</td>
                    <td>{{ date('d M Y', strtotime($row->tanggal_kadaluarsa)) }}</td>
                    <td>
                        @if($sisaHari >= 0)
                            <span class="badge badge-aktif">🟢 Aktif ({{ $sisaHari }} Hari Lagi)</span>
                        @else
                            <span class="badge badge-habis">🔴 Habis (Lewat {{ abs($sisaHari) }} Hari)</span>
                        @endif
                    </td>
                    <td><span style="font-weight: 700; color: #475569;">🔑 {{ $row->total_checkin ?? 0 }} x</span></td>
                    <td>
                        <div class="action-btns">
                            @if($sisaHari >= 0)
                                @if(in_array($row->id, $memberSudahCheckinHariIni ?? []))
                                    <button class="btn-action btn-checkin" style="background-color: #cbd5e1; color: #94a3b8; cursor: not-allowed;" title="Member ini sudah melakukan check-in hari ini!" disabled>Sudah Check-In</button>
                                @else
                                    <form action="{{ route('member.checkin', $row->id) }}" method="POST" onsubmit="return confirm('Proses Check-In masuk gym untuk member {{ $row->nama_member }}?')">
                                        @csrf
                                        <button type="submit" class="btn-action btn-checkin">Check-In</button>
                                    </form>
                                @endif
                            @else
                                <button class="btn-action btn-checkin" style="background-color: #cbd5e1; color: #94a3b8; cursor: not-allowed;" title="Masa aktif habis, silakan perpanjang!" disabled>Check-In</button>
                            @endif

                            <button class="btn-action btn-perpanjang" data-member="{{ json_encode($row) }}" onclick="bukaModalPerpanjang(JSON.parse(this.getAttribute('data-member')))">Perpanjang</button>

                            <button class="btn-action btn-edit" data-member="{{ json_encode($row) }}" onclick="bukaModalEdit(JSON.parse(this.getAttribute('data-member')))">Edit</button>
                            
                            <form action="{{ route('member.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Hapus total keanggotaan member ini dari sistem?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action btn-hapus">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="barisKosongBawaanMember">
                    <td colspan="7" style="text-align: center; color: #94a3b8; padding: 20px;">
                        Belum ada data member terdaftar di database.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="modal-overlay" id="modalMember">
        <div class="modal-box">
            <div class="modal-header">
                <h2 id="modalTitle">Pendaftaran Member Baru</h2>
                <button class="btn-close" onclick="tutupModal()">&times;</button>
            </div>
            
            <form id="formMember" action="" method="POST">
                @csrf
                <div id="methodField"></div>
                
                <div id="infoBoxPerpanjang" class="info-box-modal" style="display: none;"></div>

                <div class="form-group" id="groupNama">
                    <label>Nama Lengkap Member:</label>
                    <input type="text" name="nama_member" id="inputNama" placeholder="Nama lengkap..." required>
                </div>

                <div class="form-group" id="groupTelepon">
                    <label>No. HP / WhatsApp:</label>
                    <input type="text" name="nomor_telepon" id="inputTelepon" placeholder="Contoh: 08123xxxx" required oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                </div>

                <div class="form-group" id="groupNominal">
                    <label id="labelNominal">Nominal Pembayaran Awal (Rp):</label>
                    <input type="number" name="nominal" id="inputNominal" min="0" value="100000" readonly>
                </div>

                <div class="form-group" id="groupKadaluarsa" style="display: none;">
                    <label>Tanggal Kadaluarsa (Manual Tweak):</label>
                    <input type="date" name="tanggal_kadaluarsa" id="inputKadaluarsa">
                </div>

                <div class="info-box-modal" id="infoBoxSistem">
                    💡 <strong>Info Kasir:</strong> Pendaftaran member baru otomatis akan memberikan durasi aktif selama <strong>30 Hari Kedepan</strong> sejak hari ini.
                </div>

                <button type="submit" class="btn-simpan" id="btnSubmitForm" style="background-color: #10b981;">PROSES DATA</button>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    const modal = document.getElementById('modalMember');
    const form = document.getElementById('formMember');
    const modalTitle = document.getElementById('modalTitle');
    const methodField = document.getElementById('methodField');
    const btnSubmit = document.getElementById('btnSubmitForm');
    
    const hargaDefaultBulanan = {{ $hargaBulanan }};

    const groupNama = document.getElementById('groupNama');
    const groupTelepon = document.getElementById('groupTelepon');
    const groupNominal = document.getElementById('groupNominal');
    const groupKadaluarsa = document.getElementById('groupKadaluarsa');
    const labelNominal = document.getElementById('labelNominal');
    
    const infoBoxPerpanjang = document.getElementById('infoBoxPerpanjang');
    const infoBoxSistem = document.getElementById('infoBoxSistem');

    const inputNama = document.getElementById('inputNama');
    const inputTelepon = document.getElementById('inputTelepon');
    const inputNominal = document.getElementById('inputNominal');
    const inputKadaluarsa = document.getElementById('inputKadaluarsa');

    const inputCari = document.getElementById('inputCariMember');
    const tabelBody = document.getElementById('tabelBodyMember');

    inputCari.addEventListener('keyup', function() {
        const keyword = inputCari.value.toLowerCase();
        const rows = tabelBody.getElementsByClassName('baris-member');
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

        const pesanLama = document.getElementById('pesanKosongCariMember');
        if (pesanLama) pesanLama.remove();

        const bawaanKosong = document.getElementById('barisKosongBawaanMember');
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
            tr.id = 'pesanKosongCariMember';
            tr.innerHTML = `<td colspan="7" style="text-align: center; color: #94a3b8; padding: 20px;">Tidak ada data member dengan kata kunci "${inputCari.value}"</td>`;
            tabelBody.appendChild(tr);
        }
    });

    function bukaModalTambah() {
        modalTitle.innerText = "Daftarkan Member Baru";
        form.action = "{{ route('member.store') }}";
        methodField.innerHTML = "";
        btnSubmit.innerText = "DAFTARKAN MEMBER (BAYAR BULANAN)";
        btnSubmit.style.backgroundColor = "#10b981";

        groupNama.style.display = "block";
        groupTelepon.style.display = "block";
        groupNominal.style.display = "block";
        groupKadaluarsa.style.display = "none";
        infoBoxPerpanjang.style.display = "none";
        
        labelNominal.innerText = "Nominal Pembayaran Awal (Rp):";
        infoBoxSistem.innerHTML = "💡 <strong>Info Kasir:</strong> Pendaftaran member baru otomatis akan memberikan durasi aktif selama <strong>30 Hari Kedepan</strong> sejak hari ini.";

        inputNama.value = "";
        inputTelepon.value = "";
        inputNominal.value = hargaDefaultBulanan; // Default bulanan aman
        inputNama.required = true;
        inputTelepon.required = true;

        modal.classList.add('show');
    }

    function bukaModalEdit(data) {
        modalTitle.innerText = "Edit Biodata Member";
        form.action = "/member/" + data.id;
        methodField.innerHTML = `@method('PUT')`;
        btnSubmit.innerText = "PERBARUI BIODATA";
        btnSubmit.style.backgroundColor = "#f59e0b";

        groupNama.style.display = "block";
        groupTelepon.style.display = "block";
        groupNominal.style.display = "none";
        groupKadaluarsa.style.display = "block";
        infoBoxPerpanjang.style.display = "none";
        infoBoxSistem.innerHTML = "⚠️ <strong>Perhatian:</strong> Mengubah tanggal kadaluarsa secara manual akan langsung memotong/menambah masa aktif member tanpa mencatat transaksi keuangan baru.";

        inputNama.value = data.nama_member;
        inputTelepon.value = data.nomor_telepon;
        inputKadaluarsa.value = data.tanggal_kadaluarsa;
        inputNama.required = true;
        inputTelepon.required = true;

        modal.classList.add('show');
    }

    function bukaModalPerpanjang(data) {
    modalTitle.innerText = "Perpanjang Masa Aktif Bulanan";
    form.action = "/member/perpanjang/" + data.id;
    methodField.innerHTML = "";
    btnSubmit.innerText = "KONFIRMASI PERPANJANG";
    btnSubmit.style.backgroundColor = "#38bdf8";

    groupNama.style.display = "none";
    groupTelepon.style.display = "none";
    groupKadaluarsa.style.display = "none";
    
    groupNominal.style.display = "block";
    labelNominal.innerText = "Biaya Perpanjang Bulanan (Rp):";
    
    infoBoxPerpanjang.style.display = "block";
    infoBoxPerpanjang.innerHTML = `
        👤 <strong>Member:</strong> ${data.nama_member} <br>
        📞 <strong>No. Telp:</strong> ${data.nomor_telepon} <br>
        📅 <strong>Masa Aktif Sekarang:</strong> ${data.tanggal_kadaluarsa}
    `;
    
    infoBoxSistem.innerHTML = "⚙️ <strong>Sistem Otomatis:</strong> Eksekusi tombol ini akan memperpanjang masa expired member selama <strong>+30 Hari</strong> secara akumulatif, serta otomatis mencatatkan uang masuk ke laporan kas keuangan!";

    inputNama.required = false;
    inputTelepon.required = false;

    // 🛡️ ANTI-MINUS FRONTEND SINKRONISASI
    if (data.nominal < 0) {
        inputNominal.value = "" + Math.abs(data.nominal); // Tampilkan nominal minus sebagai positif
    } else {
        inputNominal.value = hargaDefaultBulanan; // Dikunci default bulanan aman
    }

    modal.classList.add('show');
    }

    function tutupModal() {
        modal.classList.remove('show');
    }
</script>
@endpush