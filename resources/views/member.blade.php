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
    }

    .input-filter {
        padding: 10px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        background-color: #fff;
        width: 220px;
        color: #0f172a;
    }
    .input-filter:focus { border-color: #38bdf8; }

    .table-container {
        background-color: #fff;
        padding: 24px;
        border-radius: 10px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        min-width: 1050px;
    }

    th, td {
        padding: 12px 10px;
        border-bottom: 1px solid #e2e8f0;
        font-size: 13px;
        vertical-align: middle;
        color: #0f172a;
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
        gap: 4px;
        flex-wrap: nowrap;
        align-items: center;
        justify-content: center;
    }

    .btn-action {
        color: white;
        border: none;
        padding: 6px 10px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 11px;
        font-weight: 600;
        transition: opacity 0.2s;
        white-space: nowrap;
    }
    .btn-action:hover { opacity: 0.9; }

    .btn-checkin { background-color: #10b981; }
    .btn-perpanjang { background-color: #38bdf8; }
    .btn-edit { background-color: #f59e0b; }
    .btn-hapus { background-color: #ef4444; }

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
    .form-group input, .form-group select { width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; background-color: #fff; color: #0f172a; box-sizing: border-box; }
    .form-group input:focus, .form-group select:focus { border-color: #38bdf8; }

    .btn-simpan {
        width: 100%; color: white; border: none;
        padding: 12px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 15px;
        transition: opacity 0.2s;
    }
    .btn-simpan.loading {
        pointer-events: none;
        opacity: 0.8;
    }

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

    /* Styling Kotak QR Code Mini Client-Side */
    .qr-box-mini {
        width: 50px;
        height: 50px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        padding: 2px;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .qr-box-mini:hover { transform: scale(1.08); }
    .qr-box-mini canvas, .qr-box-mini img {
        width: 44px !important;
        height: 44px !important;
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

    @if(session('eror'))
        <div id="notifAlert" class="notif-popup notif-gagal">
            <span>{{ session('eror') }}</span>
            <button class="notif-close" onclick="tutupNotif()">&times;</button>
        </div>
    @endif

    <div class="top-bar" style="margin-bottom: 20px;">
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button class="btn-tambah" onclick="bukaModalTambah()">[+] DAFTARKAN MEMBER BARU VIA FORM</button>
            
            <a href="{{ route('checkin.scanner') }}" style="background-color: #4f46e5; color: #fff; text-decoration: none; padding: 12px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2); transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#4338ca'" onmouseout="this.style.backgroundColor='#4f46e5'">
                📷 Buka Scanner Kamera Check-In
            </a>
        </div>
        
        <div class="filter-box">
            <form action="{{ route('member.index') }}" method="GET" id="formFilterMember" onsubmit="return false;">
                <input type="text" id="inputCariMember" name="cari" class="input-filter" placeholder="Cari nama member..." value="{{ request('cari') }}" autocomplete="off">
            </form>
        </div>
    </div>

    <div class="table-container" id="wrapperTabelMember">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama Member</th>
                    <th>No. Telepon</th>
                    <th>Masa Aktif Berakhir</th>
                    <th>Status / Sisa Hari</th>
                    <th style="text-align: center;">Total Check-in</th>
                    <th style="text-align: center;">QR Code</th>
                    <th style="text-align: center;">Aksi Khusus Kasir</th>
                </tr>
            </thead>
            <tbody id="skeletonBodyMember" style="display: none;">
                @for ($i = 0; $i < 5; $i++)
                <tr>
                    <td><div class="skeleton" style="width: 25px; height: 16px;"></div></td>
                    <td><div class="skeleton" style="width: 140px; height: 16px;"></div></td>
                    <td><div class="skeleton" style="width: 100px; height: 16px;"></div></td>
                    <td><div class="skeleton" style="width: 100px; height: 16px;"></div></td>
                    <td><div class="skeleton" style="width: 110px; height: 22px;"></div></td>
                    <td><div class="skeleton" style="width: 50px; height: 16px; margin: 0 auto;"></div></td>
                    <td><div class="skeleton" style="width: 45px; height: 45px; margin: 0 auto;"></div></td>
                    <td><div class="skeleton" style="width: 180px; height: 28px; margin: 0 auto;"></div></td>
                </tr>
                @endfor
            </tbody>

            <tbody id="tabelBodyMember">
                @forelse($daftarMember as $index => $row)
                @php
                    $tanggalSekarang = \Carbon\Carbon::today();
                    $tanggalExpired = \Carbon\Carbon::parse($row->tanggal_kadaluarsa);
                    $sisaHari = $tanggalSekarang->diffInDays($tanggalExpired, false);
                    $tokenQrAman = Crypt::encryptString(json_encode([
                        'member_id' => $row->id,
                        'expires_at' => now()->addHours(24)->toIso8601String(),
                    ]));
                @endphp
                <tr class="baris-member">
                    <td>{{ ($daftarMember->currentPage() - 1) * $daftarMember->perPage() + $index + 1 }}</td>
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
                    <td style="text-align: center;"><span style="font-weight: 700; color: #475569;">🔑 {{ $row->total_checkin ?? 0 }} x</span></td>
                    
                    <!-- Container QR Code Client-Side -->
                    <td style="text-align: center;">
                        <div id="qr-mini-{{ $row->id }}" class="qr-box-mini" title="Klik untuk memperbesar QR Code" onclick="bukaModalQr('{{ $tokenQrAman }}', '{{ $row->nama_member }}', '{{ $row->id }}')"></div>
                        <div style="font-size: 10px; color: #64748b; margin-top: 2px; font-weight: 600;">ID: {{ $row->id }}</div>
                    </td>

                    <td style="text-align: center;">
                        <div class="action-btns" style="justify-content: center;">
                            @if($sisaHari >= 0)
                                @if(in_array((int)$row->id, array_map('intval', $memberSudahCheckinHariIni ?? [])))
                                    <button class="btn-action btn-checkin" style="background-color: #cbd5e1; color: #94a3b8; cursor: not-allowed;" title="Member ini sudah melakukan check-in hari ini!" disabled>Sudah Check-In</button>
                                @else
                                    <form action="{{ route('member.checkin', $row->id) }}" method="POST" class="form-action-ajax" onsubmit="return confirm('Proses Check-In masuk gym untuk member {{ $row->nama_member }}?')">
                                        @csrf
                                        <button type="submit" class="btn-action btn-checkin">Check-In</button>
                                    </form>
                                @endif
                            @else
                                <button class="btn-action btn-checkin" style="background-color: #cbd5e1; color: #94a3b8; cursor: not-allowed;" title="Masa aktif habis, silakan perpanjang!" disabled>Check-In</button>
                            @endif

                            <button class="btn-action btn-perpanjang" data-member="{{ json_encode($row) }}" onclick="bukaModalPerpanjang(JSON.parse(this.getAttribute('data-member')))">Perpanjang</button>

                            <button class="btn-action btn-edit" data-member="{{ json_encode($row) }}" onclick="bukaModalEdit(JSON.parse(this.getAttribute('data-member')))">Edit</button>
                            
                            <form action="{{ route('member.destroy', $row->id) }}" method="POST" class="form-action-ajax" onsubmit="return confirm('Hapus total keanggotaan member ini dari sistem?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action btn-hapus">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="barisKosongBawaanMember">
                    <td colspan="8" style="text-align: center; color: #94a3b8; padding: 20px;">
                        Belum ada data member terdaftar di database.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if ($daftarMember->hasPages())
            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%; flex-wrap: wrap; gap: 12px; border-top: 1px solid #f1f5f9; padding-top: 20px; margin-top: 20px;">
                <div style="font-size: 13px; color: #64748b; font-weight: 500;">
                    Menampilkan <span style="font-weight: 600; color: #0f172a;">{{ $daftarMember->firstItem() ?? 0 }}</span> - <span style="font-weight: 600; color: #0f172a;">{{ $daftarMember->lastItem() ?? 0 }}</span> dari <span style="font-weight: 600; color: #0f172a;">{{ $daftarMember->total() }}</span> data
                </div>

                <div style="display: flex; gap: 6px; align-items: center;">
                    @if ($daftarMember->onFirstPage())
                        <span style="display: flex; align-items: center; justify-content: center; height: 38px; padding: 0 14px; border: 1px solid #e2e8f0; border-radius: 8px; color: #cbd5e1; background-color: #f8fafc; font-size: 13px; font-weight: 600; cursor: not-allowed; user-select: none;">&laquo; Previous</span>
                    @else
                        <a href="{{ $daftarMember->previousPageUrl() }}" onclick="tampilkanSkeletonMember()" class="pagination-btn" style="display: flex; align-items: center; justify-content: center; height: 38px; padding: 0 14px; border: 1px solid #cbd5e1; border-radius: 8px; color: #475569; background-color: #fff; font-size: 13px; font-weight: 600; text-decoration: none; transition: all 0.2s ease;">&laquo; Previous</a>
                    @endif

                    @foreach ($daftarMember->linkCollection() as $link)
                        @if ($link['label'] !== '&laquo; Previous' && $link['label'] !== 'Next &raquo;')
                            @if ($link['url'] === null)
                                <span style="display: flex; align-items: center; justify-content: center; height: 38px; padding: 0 6px; color: #94a3b8; font-size: 13px; font-weight: 500;">{!! $link['label'] !!}</span>
                            @else
                                @if ($link['active'])
                                    <span style="display: flex; align-items: center; justify-content: center; min-width: 38px; height: 38px; padding: 0 12px; border: 1px solid #3b82f6; border-radius: 8px; color: #fff; background-color: #3b82f6; font-size: 13px; font-weight: 600; box-shadow: 0 4px 10px rgba(59, 130, 246, 0.25); user-select: none;">{!! $link['label'] !!}</span>
                                @else
                                    <a href="{{ $link['url'] }}" onclick="tampilkanSkeletonMember()" class="pagination-btn" style="display: flex; align-items: center; justify-content: center; min-width: 38px; height: 38px; padding: 0 12px; border: 1px solid #cbd5e1; border-radius: 8px; color: #475569; background-color: #fff; font-size: 13px; font-weight: 600; text-decoration: none; transition: all 0.2s ease;">{!! $link['label'] !!}</a>
                                @endif
                            @endif
                        @endif
                    @endforeach

                    @if ($daftarMember->hasMorePages())
                        <a href="{{ $daftarMember->nextPageUrl() }}" onclick="tampilkanSkeletonMember()" class="pagination-btn" style="display: flex; align-items: center; justify-content: center; height: 38px; padding: 0 14px; border: 1px solid #cbd5e1; border-radius: 8px; color: #475569; background-color: #fff; font-size: 13px; font-weight: 600; text-decoration: none; transition: all 0.2s ease;">Next &raquo;</a>
                    @else
                        <span style="display: flex; align-items: center; justify-content: center; height: 38px; padding: 0 14px; border: 1px solid #e2e8f0; border-radius: 8px; color: #cbd5e1; background-color: #f8fafc; font-size: 13px; font-weight: 600; cursor: not-allowed; user-select: none;">Next &raquo;</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- MODAL POPUP LIHAT & DOWNLOAD QR CODE (CLIENT-SIDE) -->
    <div class="modal-overlay" id="modalQrCode">
        <div class="modal-box" style="text-align: center; width: 380px;">
            <div class="modal-header">
                <h2 id="qrModalTitle">QR Code Member</h2>
                <button class="btn-close" onclick="tutupModalQr()">&times;</button>
            </div>
            
            <div style="margin: 20px 0; display: flex; flex-direction: column; align-items: center;">
                <div id="qrContainerLarge" style="padding: 10px; background: white; border: 1px solid #cbd5e1; border-radius: 8px; display: inline-block;"></div>
                <p id="qrModalSubtitle" style="font-size: 13px; color: #64748b; margin-top: 10px; font-weight: 600;"></p>
            </div>

            <button type="button" onclick="downloadQrCode()" class="btn-simpan" style="background-color: #4f46e5; margin-top: 5px;">
                📥 Download QR Code
            </button>
        </div>
    </div>

    <!-- MODAL TAMBAH / EDIT MEMBER -->
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
<!-- Load Library Lokal qrcode.min.js -->
<script src="{{ asset('js/qrcode.min.js') }}"></script>
<script>
    // Render otomatis QR Code kecil di setiap baris tabel secara client-side
    document.addEventListener("DOMContentLoaded", function() {
        @foreach($daftarMember as $row)
            @php
                $tokenAmanRow = Crypt::encryptString(json_encode([
                    'member_id' => $row->id,
                    'expires_at' => now()->addHours(24)->toIso8601String(),
                ]));
            @endphp
            if(document.getElementById("qr-mini-{{ $row->id }}")) {
                new QRCode(document.getElementById("qr-mini-{{ $row->id }}"), {
                    text: "{{ $tokenAmanRow }}",
                    width: 44,
                    height: 44
                });
            }
        @endforeach
    });

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
    const modalQrCode = document.getElementById('modalQrCode');
    const qrContainerLarge = document.getElementById('qrContainerLarge');
    const qrModalTitle = document.getElementById('qrModalTitle');
    const qrModalSubtitle = document.getElementById('qrModalSubtitle');
    
    let currentNamaFileQr = "QRCode.png";

    function bukaModalQr(tokenEnc, namaMember, idMember) {
        qrModalTitle.innerText = "QR Code: " + namaMember;
        qrModalSubtitle.innerText = "ID Member: " + idMember;
        currentNamaFileQr = `QRCode-${namaMember.replace(/\s+/g, '_')}.png`;
        
        // Bersihkan kontainer modal besar sebelumnya
        qrContainerLarge.innerHTML = "";
        
        // Render QR code besar secara dinamis di sisi client via qrcode.min.js
        new QRCode(qrContainerLarge, {
            text: tokenEnc,
            width: 200,
            height: 200
        });

        modalQrCode.classList.add('show');
    }

    function downloadQrCode() {
        const canvas = qrContainerLarge.querySelector('canvas');
        if (canvas) {
            const imageURL = canvas.toDataURL("image/png");
            const a = document.createElement('a');
            a.href = imageURL;
            a.download = currentNamaFileQr;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        } else {
            alert("Gagal mengunduh QR Code.");
        }
    }

    function tutupModalQr() { modalQrCode.classList.remove('show'); }

    window.addEventListener('click', function(event) {
        if (event.target === modalQrCode) { tutupModalQr(); }
    });

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
    });

    function tampilkanSkeletonMember() {
        const tbody = document.getElementById('tabelBodyMember');
        const skeleton = document.getElementById('skeletonBodyMember');
        if (tbody && skeleton) {
            tbody.style.display = 'none';
            skeleton.style.display = 'table-row-group';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        let searchTimer;
        if (inputCari) {
            inputCari.addEventListener('input', function() {
                clearTimeout(searchTimer);
                const keyword = this.value;
                tampilkanSkeletonMember();

                searchTimer = setTimeout(() => {
                    const url = `{{ route('member.index') }}?cari=${encodeURIComponent(keyword)}`;
                    
                    fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newContainer = doc.getElementById('wrapperTabelMember');
                        
                        if (newContainer) {
                            document.getElementById('wrapperTabelMember').innerHTML = newContainer.innerHTML;
                        }

                        const newInputCari = document.getElementById('inputCariMember');
                        if (newInputCari) {
                            newInputCari.focus();
                            const valLen = newInputCari.value.length;
                            newInputCari.setSelectionRange(valLen, valLen);
                        }
                    })
                    .catch(err => console.error('Gagal melakukan pencarian:', err));
                }, 300);
            });
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
        inputNominal.value = hargaDefaultBulanan;
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

        if (data.nominal < 0) {
            inputNominal.value = "" + Math.abs(data.nominal);
        } else {
            inputNominal.value = hargaDefaultBulanan;
        }

        modal.classList.add('show');
    }

    function tutupModal() { modal.classList.remove('show'); }

    if (form) {
        form.addEventListener('submit', function(e) {
            if (btnSubmit) {
                btnSubmit.classList.add("loading");
                btnSubmit.innerHTML = `
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
</script>
@endpush