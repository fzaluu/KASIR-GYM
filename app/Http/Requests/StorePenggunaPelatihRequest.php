<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePenggunaPelatihRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_pengguna'           => 'required|string|max:255',
            'nomor_telepon_pengguna'  => 'required|string|max:20',
            'pelatih_id'              => 'required|exists:pelatih,id',
            'tipe_jasa'               => 'required|in:perbulan,perhari',
            'tarif_jasa'              => 'required|integer|min:0',
        ];
    }
    public function messages(): array
    {
        return [
            'tarif_jasa.integer'      => 'Gagal! Tarif sewa harus berupa angka bulat murni.',
            'tarif_jasa.min'          => 'Gagal! Tarif sewa tidak boleh bernilai minus.',
        ];
    }
}
