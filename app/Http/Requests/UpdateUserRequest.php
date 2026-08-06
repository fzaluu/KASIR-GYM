<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        // Ambil ID user dengan memastikan yang diambil adalah nilai ID-nya (bukan objek model)
        $userId = $this->route('user') instanceof \App\Models\User ? $this->route('user')->id : $this->route('user');

        return [
            'name'     => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $userId],
            'password' => ['nullable', 'string', 'min:8'],
            'role_id'  => ['required', 'exists:roles,id'],
            'status'   => ['required', 'in:aktif,nonaktif'],
        ];
    }
}