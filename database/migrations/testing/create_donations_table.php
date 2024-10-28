<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table): void {
            $table->uuid('id');
            $table->foreignUuid('employee_id');
            $table->foreignUuid('branch_id');
            $table->foreignUuid('donor_id');
            $table->string('identification_number');
            $table->string('transaction_date');
            $table->string('transaction_status');
            $table->decimal('amount', 18, 2)->default(0);
            $table->decimal('total_amount', 18, 2)->default(0);
            $table->timestamps();

            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
