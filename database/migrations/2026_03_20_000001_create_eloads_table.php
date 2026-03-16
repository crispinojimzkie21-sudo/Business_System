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
        Schema::create('eloads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('network'); // Smart, Globe, DITO
            $table->decimal('price', 10, 2);
            $table->foreignId('category_id')->constrained('eload_categories')->onDelete('cascade');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eloads');
    }
};

