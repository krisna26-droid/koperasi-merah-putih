<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('actor_username', 50)->nullable()->index();
            $table->string('actor_name', 100)->nullable();
            $table->string('action', 20)->index();
            $table->string('subject_type', 80)->index();
            $table->string('subject_id', 100)->index();
            $table->json('changed_fields')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
