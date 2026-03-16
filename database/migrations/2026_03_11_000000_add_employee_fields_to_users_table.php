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
            $table->string('phone')->nullable()->after('email');
            $table->string('address')->nullable()->after('phone');
            $table->date('hire_date')->nullable()->after('address');
            $table->string('department')->nullable()->after('hire_date');
            $table->string('employee_id')->nullable()->after('department')->unique();
            $table->enum('employment_status', ['active', 'inactive', 'on_leave', 'terminated'])->default('active')->after('employee_id');
            $table->text('notes')->nullable()->after('employment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'address',
                'hire_date',
                'department',
                'employee_id',
                'employment_status',
                'notes',
            ]);
        });
    }
};
