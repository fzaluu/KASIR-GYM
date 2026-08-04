<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePelatihRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_pelatih' => ['required', 'unique:pelatih,nama_pelatih', 'max:255'],
            'nomor_telepon' => ['required', 'string', 'max:20'],
            'tarif_bulanan' => ['required', 'integer', 'min:0'],
            'tarif_harian' => ['required', 'integer', 'min:0'],
            'status_hadir' => ['required', 'in:hadir,tidak_hadir'],
        ];
    }
}
