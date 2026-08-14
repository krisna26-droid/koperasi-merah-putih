<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnggotaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->peran === 'Admin';
    }

    public function rules(): array
    {
        return [
            'id_anggota' => ['required', 'string', 'max:20', 'unique:tb_anggota,id_anggota'],
            'nama' => ['required', 'string', 'max:100'],
            'alamat' => ['required', 'string'],
            'no_telepon' => ['required', 'numeric', 'digits_between:8,15'],
            'status' => ['required', 'in:Aktif,Cuti'],
        ];
    }
}