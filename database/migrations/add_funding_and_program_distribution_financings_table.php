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
            $table->string('donor_identification_number')->nullable();
            $table->foreignId('donation_detail_funding_type_id')->nullable();
            $table->foreignId('donation_detail_program_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('distribution_financings', function (Blueprint $table): void {
            $table->dropColumn('donor_id');
            $table->dropColumn('donor_identification_number');
            $table->dropColumn('donation_detail_funding_type_id');
            $table->dropColumn('donation_detail_program_id');
        });
    }
};
