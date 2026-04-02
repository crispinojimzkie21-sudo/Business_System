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
        Schema::table('eload_transactions', function (Blueprint $table) {
            $table->string('customer_name')->nullable()->after('transaction_id');
            $table->string('customer_contact')->nullable()->after('customer_name');
            $table->string('provider')->nullable()->after('customer_contact');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eload_transactions', function (Blueprint $table) {
            $table->dropColumn(['customer_name', 'customer_contact', 'provider']);
        });
    }
};
