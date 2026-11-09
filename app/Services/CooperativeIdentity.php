<?php

namespace App\Services;

use App\Models\CooperativeSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class CooperativeIdentity
{
    public function current(): CooperativeSetting
    {
        if (!Schema::hasTable('cooperative_settings')) {
            return new CooperativeSetting();
        }

        return Cache::rememberForever('cooperative.identity', function () {
            $settings = CooperativeSetting::query()->find(1);

            if ($settings) {
                return $settings;
            }

            $settings = new CooperativeSetting();
            $settings->id = 1;

            return $settings;
        });
    }

    public function forget(): void
    {
        Cache::forget('cooperative.identity');
    }
}
