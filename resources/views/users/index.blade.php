@extends('layouts.app')

@section('title', 'Manajemen User - Virgo Gym')
@section('page_title', 'Manajemen User Sistem')

@push('styles')
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
    

    /* === NOTIFIKASI MENGAMBANG (SAMA SEPERTI DASHBOARD) === */
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
</style>
@endpush

@section('konten')
<div style="display: flex; flex-direction: column; gap: 20px;">

    <!-- Notifikasi Mengambang Sukses / Gagal (Sama seperti Dashboard) -->
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

    <!-- Bar Aksi -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <button class="btn-tambah" type="button" onclick="bukaModalTambah()" onmouseover="this.style.backgroundColor='#0ea5e9'" onmouseout="this.style.backgroundColor='#38bdf8'" style="padding: 12px 20px; background: #38bdf8; color: #fff; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: background-color 0.2s ease;">
        [+] Tambah User Baru
    </button>

        <div style="display: flex; gap: 8px;">
            <input type="text" id="liveSearchInput" value="{{ $search ?? '' }}" placeholder="Live search nama atau username..." style="padding: 10px 14px; background: #fff; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; width: 280px; color: #0f172a;">
        </div>
    </div>

    <!-- Tabel Data User -->
    <div style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px; color: #1e293b;">
            <thead style="background: #f1f5f9; border-bottom: 1px solid #e2e8f0; color: #475569; font-size: 13px;">
                <tr>
                    <th style="padding: 14px 16px;">No</th>
                    <th style="padding: 14px 16px;">Nama Lengkap</th>
                    <th style="padding: 14px 16px;">Username</th>
                    <th style="padding: 14px 16px;">Role</th>
                    <th style="padding: 14px 16px;">Status</th>
                    <th style="padding: 14px 16px;">Login Terakhir</th>
                    <th style="padding: 14px 16px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody id="tableUserBody">
                @forelse($users as $index => $u)
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    <td style="padding: 14px 16px;">{{ $users->firstItem() + $index }}</td>
                    <td style="padding: 14px 16px; font-weight: 600;">{{ $u->name }}</td>
                    <td style="padding: 14px 16px; color: #64748b;">{{ $u->username }}</td>
                    <td style="padding: 14px 16px;">
                        <span style="padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; background: {{ $u->role?->slug === 'admin' ? '#ede9fe' : '#e0f2fe' }}; color: {{ $u->role?->slug === 'admin' ? '#7c3aed' : '#0284c7' }};">
                            {{ ucfirst($u->role?->name ?? $u->role?->slug) }}
                        </span>
                    </td>
                    <td style="padding: 14px 16px;">
                        <span style="padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; background: {{ $u->status === 'aktif' ? '#dcfce7' : '#fee2e2' }}; color: {{ $u->status === 'aktif' ? '#16a34a' : '#dc2626' }};">
                            {{ ucfirst($u->status) }}
                        </span>
                    </td>
                    <td style="padding: 14px 16px; color: #64748b; font-size: 13px;">
                        {{ $u->last_login_at ? \Carbon\Carbon::parse($u->last_login_at)->format('d/m/Y H:i') : 'Belum pernah login' }}
                    </td>
                    <td style="padding: 14px 16px; text-align: center;">
                        <div style="display: flex; justify-content: center; gap: 8px;">
                            <button type="button" onclick='bukaModalDetail(@json($u))' style="padding: 6px 12px; background: #d97706; color: white; border: none; border-radius: 6px; font-size: 12px; cursor: pointer;">Detail</button>
                            <button type="button" onclick='bukaModalEdit(@json($u))' style="padding: 6px 12px; background: #0284c7; color: white; border: none; border-radius: 6px; font-size: 12px; cursor: pointer;">Edit</button>
                            <form action="{{ route('users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus user {{ $u->username }}?');" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="padding: 6px 12px; background: #dc2626; color: white; border: none; border-radius: 6px; font-size: 12px; cursor: pointer;" {{ auth()->id() === $u->id ? 'disabled style=opacity:0.5;cursor:not-allowed;' : '' }}>Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding: 30px; text-align: center; color: #64748b;">Tidak ada data user ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div style="padding: 16px;">
            {{ $users->links('vendor.pagination.custom') }}
        </div>
    </div>

</div>

<!-- MODAL DETAIL USER -->
<div id="modalDetail" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(15,23,42,0.6); backdrop-filter: blur(6px); z-index: 1000; justify-content: center; align-items: center;">
    <div style="background: white; padding: 24px; border-radius: 12px; width: 100%; max-width: 480px; color: #1e293b; max-height: 90vh; overflow-y: auto;">
        <h3 style="margin-bottom: 16px; font-size: 18px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">Detail Informasi User</h3>
        <div style="display: flex; flex-direction: column; gap: 8px; font-size: 14px; margin-bottom: 16px;">
            <div><strong>Nama &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;:</strong> <span id="det_name"></span></div>
            <div><strong>Username &nbsp; &nbsp; &nbsp;:</strong> <span id="det_username"></span></div>
            <div><strong>Role &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; :</strong> <span id="det_role"></span></div>
            <div><strong>Status &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; :</strong> <span id="det_status"></span></div>
            <div><strong>Last Login &nbsp; &nbsp; :</strong> <span id="det_last_login"></span></div>
        </div>
        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 16px 0;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
            <div style="background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <div style="font-size: 11px; color: #64748b; font-weight: 600;">Total Transaksi</div>
                <div id="det_total_transaksi" style="font-size: 18px; font-weight: 700; color: #0f172a;">0</div>
            </div>
            <div style="background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <div style="font-size: 11px; color: #64748b; font-weight: 600;">Total Pendapatan</div>
                <div id="det_total_pendapatan" style="font-size: 16px; font-weight: 700; color: #16a34a;">Rp 0</div>
            </div>
        </div>
        <div style="font-weight: 600; font-size: 13px; margin-bottom: 8px; color: #475569;">Aktivitas Terakhir</div>
        <div id="det_aktivitas_terakhir" style="background: #f8fafc; padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 12px; color: #334155; max-height: 120px; overflow-y: auto;">
            Memuat aktivitas...
        </div>
        <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
            <button type="button" onclick="tutupModalDetail()" style="padding: 8px 16px; background: #64748b; color: white; border: none; border-radius: 6px; cursor: pointer;">Tutup</button>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH USER -->
<div id="modalTambah" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(15,23,42,0.6); backdrop-filter: blur(6px); z-index: 1000; justify-content: center; align-items: center;">
    <div style="background: white; padding: 24px; border-radius: 12px; width: 100%; max-width: 440px; color: #1e293b;">
        <h3 style="margin-bottom: 16px; font-size: 18px;">Tambah User Baru</h3>
        <form action="{{ route('users.store') }}" method="POST" onsubmit="loadingTambah(this)">
            @csrf
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Nama Lengkap</label>
                <input type="text" name="name" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
            </div>
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Username</label>
                <input type="text" name="username" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
            </div>
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Password (Min. 8 Karakter)</label>
                <div style="position: relative;">
                    <input type="password" name="password" id="passwordTambah" required minlength="8" style="width: 100%; padding: 10px; padding-right: 38px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    <span onclick="togglePassword('passwordTambah', 'iconTambah')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #64748b;">
                        <svg id="iconTambah" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </span>
                </div>
            </div>
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Role</label>
                <select name="role_id" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ ucfirst($role->name ?? $role->slug) }}</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Status</label>
                <select name="status" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 8px;">
                <button type="button" onclick="tutupModalTambah()" style="padding: 8px 14px; background: #e2e8f0; border: none; border-radius: 6px; cursor: pointer;">Batal</button>
                <button type="submit" id="btnSimpanTambah" style="padding: 8px 16px; background: #16a34a; color: white; border: none; border-radius: 6px; cursor: pointer;">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT USER -->
<div id="modalEdit" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(15,23,42,0.6); backdrop-filter: blur(6px); z-index: 1000; justify-content: center; align-items: center;">
    <div style="background: white; padding: 24px; border-radius: 12px; width: 100%; max-width: 440px; color: #1e293b;">
        <h3 style="margin-bottom: 16px; font-size: 18px;">Edit Data User</h3>
        <form id="formEdit" method="POST" onsubmit="loadingEdit(this)">
            @csrf
            @method('PUT')
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Nama Lengkap</label>
                <input type="text" name="name" id="edit_name" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
            </div>
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Username</label>
                <input type="text" name="username" id="edit_username" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
            </div>
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Password Baru (Kosongkan jika tidak diubah)</label>
                <div style="position: relative;">
                    <input type="password" name="password" id="passwordEdit" minlength="8" style="width: 100%; padding: 10px; padding-right: 38px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    <span onclick="togglePassword('passwordEdit', 'iconEdit')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #64748b;">
                        <svg id="iconEdit" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </span>
                </div>
            </div>
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Role</label>
                <select name="role_id" id="edit_role_id" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ ucfirst($role->name ?? $role->slug) }}</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Status</label>
                <select name="status" id="edit_status" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 8px;">
                <button type="button" onclick="tutupModalEdit()" style="padding: 8px 14px; background: #e2e8f0; border: none; border-radius: 6px; cursor: pointer;">Batal</button>
                <button type="submit" id="btnPerbaruiEdit" style="padding: 8px 16px; background: #0284c7; color: white; border: none; border-radius: 6px; cursor: pointer;">Perbarui</button>
            </div>
        </form>
    </div>
</div>

<script>
    function togglePassword(fieldId, iconId) {
        let field = document.getElementById(fieldId);
        let icon = document.getElementById(iconId);
        if (field.type === "password") {
            field.type = "text";
            icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
        } else {
            field.type = "password";
            icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
        }
    }

    function loadingTambah(form) {
        let btn = document.getElementById('btnSimpanTambah');
        btn.disabled = true;
        btn.innerHTML = 'Menyimpan...';
    }

    function loadingEdit(form) {
        let btn = document.getElementById('btnPerbaruiEdit');
        btn.disabled = true;
        btn.innerHTML = 'Memperbarui...';
    }

    function bukaModalTambah() { document.getElementById('modalTambah').style.display = 'flex'; }
    function tutupModalTambah() { document.getElementById('modalTambah').style.display = 'none'; }

    function bukaModalEdit(user) {
        document.getElementById('edit_name').value = user.name;
        document.getElementById('edit_username').value = user.username;
        document.getElementById('edit_role_id').value = user.role_id;
        document.getElementById('edit_status').value = user.status;
        document.getElementById('formEdit').action = '/users/' + user.id;
        document.getElementById('modalEdit').style.display = 'flex';
    }
    function tutupModalEdit() { document.getElementById('modalEdit').style.display = 'none'; }

    function bukaModalDetail(user) {
        document.getElementById('det_name').textContent = user.name;
        document.getElementById('det_username').textContent = user.username;
        document.getElementById('det_role').textContent = user.role ? (user.role.name || user.role.slug) : '-';
        document.getElementById('det_status').textContent = user.status;
        document.getElementById('det_last_login').textContent = user.last_login_at ? new Date(user.last_login_at).toLocaleString() : 'Belum pernah login';
        
        document.getElementById('det_total_transaksi').textContent = user.transaksis_count || 0;
        document.getElementById('det_total_pendapatan').textContent = 'Rp ' + Number(user.transaksis_sum_nominal || 0).toLocaleString('id-ID');
        
        let aktivContainer = document.getElementById('det_aktivitas_terakhir');
        if (user.activity_logs && user.activity_logs.length > 0) {
            aktivContainer.innerHTML = user.activity_logs.map(log => `<div>• [${new Date(log.created_at).toLocaleString()}] <b>${log.aksi}</b>: ${log.deskripsi}</div>`).join('');
        } else {
            aktivContainer.innerHTML = 'Tidak ada catatan aktivitas.';
        }

        document.getElementById('modalDetail').style.display = 'flex';
    }
    function tutupModalDetail() { document.getElementById('modalDetail').style.display = 'none'; }

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
            }, 6000);
        }

        const searchInput = document.getElementById('liveSearchInput');
        const tableBody = document.getElementById('tableUserBody');
        let searchTimeout;

        function tampilkanSkeleton() {
            if (tableBody) {
                let skeletonRows = '';
                for (let i = 0; i < 5; i++) {
                    skeletonRows += `
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 14px 16px;"><div class="skeleton-box" style="height: 16px; width: 30px;"></div></td>
                            <td style="padding: 14px 16px;"><div class="skeleton-box" style="height: 16px; width: 140px;"></div></td>
                            <td style="padding: 14px 16px;"><div class="skeleton-box" style="height: 16px; width: 100px;"></div></td>
                            <td style="padding: 14px 16px;"><div class="skeleton-box" style="height: 20px; width: 70px; border-radius: 20px;"></div></td>
                            <td style="padding: 14px 16px;"><div class="skeleton-box" style="height: 20px; width: 60px; border-radius: 20px;"></div></td>
                            <td style="padding: 14px 16px;"><div class="skeleton-box" style="height: 16px; width: 110px;"></div></td>
                            <td style="padding: 14px 16px; text-align: center;"><div class="skeleton-box" style="height: 26px; width: 120px; margin: auto;"></div></td>
                        </tr>
                    `;
                }
                tableBody.innerHTML = skeletonRows;
            }
        }

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                tampilkanSkeleton();
                searchTimeout = setTimeout(() => {
                    let query = this.value;
                    fetch(`{{ route('users.index') }}?search=` + encodeURIComponent(query), {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(response => response.text())
                    .then(html => {
                        let parser = new DOMParser();
                        let doc = parser.parseFromString(html, 'text/html');
                        let newTbody = doc.getElementById('tableUserBody');
                        if (newTbody) {
                            tableBody.innerHTML = newTbody.innerHTML;
                        }
                    });
                }, 400);
            });
        }

        document.addEventListener('click', function(e) {
            let target = e.target.closest('.pagination a');
            if (target) {
                tampilkanSkeleton();
            }
        });
    });
</script>
@endsection