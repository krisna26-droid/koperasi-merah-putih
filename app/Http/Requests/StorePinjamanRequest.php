<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePinjamanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->peran === 'Admin';
    }

    public function rules(): array
    {
        return [
            'id_anggota' => ['required', 'exists:tb_anggota,id_anggota'],
            'jumlah_pinjam' => ['required', 'numeric', 'min:100000'],
            'tenor' => ['required', 'integer', 'min:1', 'max:60'],
        ];
    }
}