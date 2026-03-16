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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('access_status', ['enabled', 'disabled'])->default('enabled')->after('employment_status');
            $table->text('access_restriction_reason')->nullable()->after('access_status');
            $table->timestamp('access_disabled_at')->nullable()->after('access_restriction_reason');
            $table->integer('disabled_by')->nullable()->after('access_disabled_at');
            $table->timestamp('access_enabled_at')->nullable()->after('disabled_by');
            $table->integer('enabled_by')->nullable()->after('access_enabled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'access_status',
                'access_restriction_reason',
                'access_disabled_at',
                'disabled_by',
                'access_enabled_at',
                'enabled_by'
            ]);
        });
    }
};
