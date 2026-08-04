<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransaksiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipe_kunjungan' => ['required', 'in:harian,checkin,perpanjang'],
            'nominal' => ['required', 'integer', 'min:0'],
            'nama' => ['required_if:tipe_kunjungan,harian', 'nullable', 'string', 'max:255'],
            'member_id' => ['required_if:tipe_kunjungan,checkin,perpanjang', 'nullable', 'integer'],
        ];
    }
}
