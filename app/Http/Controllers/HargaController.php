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
        $request->validate([
            'harga' => ['required', 'array'],
            'harga.*' => ['required', 'integer', 'min:0'],
        ], [
            'harga.required' => 'Data harga wajib dikirim.',
            'harga.array' => 'Format data harga tidak valid.',
            'harga.*.required' => 'Setiap harga wajib diisi.',
            'harga.*.integer' => 'Setiap harga harus berupa angka bulat.',
            'harga.*.min' => 'Harga tidak boleh bernilai negatif.',
        ]);

        foreach ($request->harga as $id => $nilai) {
            HargaPaket::where('id', $id)->update([
                'harga' => $nilai,
                'updated_by' => auth()->id(),
            ]);
        }
        return back()->with('sukses', 'Harga berhasil diupdate!');
    }
}