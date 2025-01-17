<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table): void {
            $table->string('donation_type')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table): void {
            $table->dropColumn(['donation_type']);
        });
    }
};
