<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HargaPaket; // PASTIKAN BARIS INI ADA!

class HargaController extends Controller
{
    public function index() {
        $hargaPaket = HargaPaket::all();
        return view('harga', compact('hargaPaket'));
    }

    public function updateAll(Request $request) {
    
        if (!$request->has('harga')) {
            return back()->with('gagal', 'Data tidak ditemukan');
        }

        foreach ($request->harga as $id => $nilai) {
            HargaPaket::where('id', $id)->update(['harga' => $nilai]);
        }
        return back()->with('sukses', 'Harga berhasil diupdate!');
    }
}