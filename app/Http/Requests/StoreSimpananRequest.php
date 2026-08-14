<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSimpananRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->peran === 'Admin';
    }

    public function rules(): array
    {
        return [
            'id_anggota' => ['required', 'exists:tb_anggota,id_anggota'],
            'jenis_simpanan' => ['required', 'in:Pokok,Wajib,Sukarela'],
            'nominal' => ['required', 'numeric', 'gt:0'],
        ];
    }
}