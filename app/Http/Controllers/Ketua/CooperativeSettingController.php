<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\CooperativeSetting;
use App\Services\CooperativeIdentity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CooperativeSettingController extends Controller
{
    public function edit(CooperativeIdentity $identity): View
    {
        return view('ketua.settings.identity', ['settings' => $identity->current()]);
    }

    public function update(Request $request, CooperativeIdentity $identity): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'address' => ['required', 'string', 'max:1000'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'remove_logo' => ['sometimes', 'boolean'],
        ]);

        $settings = CooperativeSetting::query()->find(1) ?? new CooperativeSetting();
        $settings->id = 1;
        $previousLogo = $settings->logo_path;
        $newLogo = null;

        if ($request->hasFile('logo')) {
            $newLogo = $request->file('logo')->store('cooperative/branding', 'public');
        }

        $settings->fill([
            'name' => $validated['name'],
            'address' => $validated['address'],
        ]);

        if ($newLogo !== null) {
            $settings->logo_path = $newLogo;
        } elseif ($request->boolean('remove_logo')) {
            $settings->logo_path = null;
        }

        $settings->save();
        $identity->forget();

        if (($newLogo !== null || $request->boolean('remove_logo')) && $previousLogo) {
            Storage::disk('public')->delete($previousLogo);
        }

        return back()->with('success', 'Identitas koperasi berhasil diperbarui.');
    }
}
