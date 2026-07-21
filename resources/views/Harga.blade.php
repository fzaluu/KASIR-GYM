@extends('layouts.app')

@section('title', 'Pengaturan Harga - Virgo Gym')
@section('page_title', 'Pengaturan Harga')

@section('konten')
<div style="background-color: #fff; padding: 24px; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; max-width: 100%;">
    
    <h3 style="margin-bottom: 20px; color: #0f172a; font-size: 18px;">Update Harga Paket</h3>

    @if(session('sukses'))
        <div style="background-color: #d1fae5; color: #065f46; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; border: 1px solid #a7f3d0;">
            {{ session('sukses') }}
        </div>
    @endif

    <form action="{{ route('harga.update_all') }}" method="POST">
        @csrf
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="text-align: left; color: #475569;">
                    <th style="padding: 10px;">Nama Paket</th>
                    <th style="padding: 10px;">Harga (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hargaPaket as $hp)
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    <td style="padding: 12px; text-transform: capitalize; font-weight: 600; color: #334155;">{{ $hp->nama_paket }}</td>
                    <td style="padding: 12px;">
                        <input type="number" name="harga[{{ $hp->id }}]" value="{{ $hp->harga }}" 
                        style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 25px;">
            <button type="submit" 
            style="width: 100%; background-color: #38bdf8; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: 600; cursor: pointer;">
                SIMPAN PERUBAHAN
            </button>
        </div>
    </form>
</div>
@endsection