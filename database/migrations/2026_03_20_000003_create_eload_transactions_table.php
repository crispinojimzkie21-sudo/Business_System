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
        Schema::create('eload_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eload_id')->constrained('eloads')->onDelete('cascade');
            $table->foreignId('eload_number_id')->constrained('eload_numbers')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('eload_number'); // Mobile number to receive load
            $table->decimal('price', 10, 2);
            $table->enum('status', ['completed', 'not_completed'])->default('not_completed');
            $table->string('transaction_id')->nullable()->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eload_transactions');
    }
};

