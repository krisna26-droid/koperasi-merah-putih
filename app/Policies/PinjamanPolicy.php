<?php

namespace App\Policies;

use App\Models\Pinjaman;
use App\Models\User;

class PinjamanPolicy
{
    /**
     * Menentukan apakah user dapat menyetujui/menolak pinjaman.
     */
    public function approve(User $user, Pinjaman $pinjaman): bool
    {
        return $user->peran === 'Ketua' && $pinjaman->status === 'Pengajuan';
    }

    /**
     * Menentukan apakah user dapat melihat detail pinjaman.
     */
    public function view(User $user, Pinjaman $pinjaman): bool
    {
        return in_array($user->peran, ['Admin', 'Ketua']);
    }
}