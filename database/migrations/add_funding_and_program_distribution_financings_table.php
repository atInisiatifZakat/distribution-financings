<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('distribution_financings', function (Blueprint $table): void {
            $table->foreignUuid('donor_id')->nullable();
            $table->foreignUuid('donor_identification_number')->nullable();
            $table->foreignUuid('funding_id')->nullable();
            $table->foreignUuid('program_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('distribution_financings', function (Blueprint $table): void {
            $table->dropColumn('donor_id');
            $table->dropColumn('donor_identification_number');
            $table->dropColumn('funding_id');
            $table->dropColumn('program_id');
        });
    }
};
