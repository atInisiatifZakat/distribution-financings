<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donation_details', function (Blueprint $table): void {
            $table->increments('id');
            $table->uuid('donation_id');
            $table->uuid('program_id')->nullable();
            $table->unsignedInteger('funding_type_id')->nullable();
            $table->decimal('amount', 18, 2)->default(0);
            $table->decimal('total_amount', 18, 2)->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_details');
    }
};
