<?php

namespace App\Providers;

use App\Models\Anggota;
use App\Models\Angsuran;
use App\Models\CooperativeSetting;
use App\Models\JurnalDetail;
use App\Models\JurnalUmum;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use App\Models\User;
use App\Observers\ActivityLogObserver;
use App\Services\CooperativeIdentity;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        foreach ([Anggota::class, Simpanan::class, Pinjaman::class, Angsuran::class, JurnalUmum::class, JurnalDetail::class, User::class, CooperativeSetting::class] as $model) {
            $model::observe(ActivityLogObserver::class);
        }

        View::composer('*', function ($view): void {
            $view->with('cooperativeSettings', app(CooperativeIdentity::class)->current());
        });
    }
}
