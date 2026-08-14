<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAngsuranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->peran === 'Admin';
    }

    public function rules(): array
    {
        return [
            'id_pinjam' => ['required', 'exists:tb_pinjaman,id_pinjam'],
        ];
    }
}