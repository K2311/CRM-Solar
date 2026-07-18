<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('plan')->default('demo'); // demo, pro, enterprise
            $table->string('plan_status')->default('active'); // active, suspended, expired
            $table->timestamp('plan_expires_at')->nullable();
            $table->string('payment_method')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['plan', 'plan_status', 'plan_expires_at', 'payment_method']);
        });
    }
};
