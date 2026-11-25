<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cooperative_settings', function (Blueprint $table) {
            $table->decimal('default_loan_interest_rate', 5, 2)->default(1.50)->after('logo_path');
        });
    }

    public function down(): void
    {
        Schema::table('cooperative_settings', function (Blueprint $table) {
            $table->dropColumn('default_loan_interest_rate');
        });
    }
};
