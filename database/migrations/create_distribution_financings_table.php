<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distribution_financings', function (Blueprint $table): void {
            $table->uuid('id');
            $table->foreignUuid('donation_id');
            $table->text('donation_number');
            $table->foreignUuid('distribution_id');
            $table->string('distribution_name');
            $table->timestamp('distribution_at')->nullable();
            $table->foreignUuid('distribution_program_id');
            $table->string('distribution_program_name');
            $table->foreignId('distribution_sector_id');
            $table->string('distribution_sector_name');
            $table->decimal('amount', 18, 2)->default(0);
            $table->timestamps();

            $table->primary('id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribution_financings');
    }
};
