<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales_invoice', function (Blueprint $table) {
            $table->id();
            $table->string('code',30)->unique();
            $table->date('date');
            $table->foreignId('contact_id')->index();
            $table->decimal('total_qty', 12, 2)->nullable();
            $table->decimal('total_dpp', 12, 2)->nullable();
            $table->decimal('total_invoice', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_invoice');
    }
};
