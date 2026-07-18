<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('brand')->nullable();
            $table->decimal('gst_rate', 5, 2)->default(18.00); // e.g. 5%, 12%, 18%
            $table->integer('capacity_watts')->nullable(); // For solar panel wattage calculation
        });

        Schema::table('installations', function (Blueprint $table) {
            $table->string('subsidy_status')->default('none'); // none, registered, docs_submitted, approved, disbursed
            $table->date('subsidy_registered_at')->nullable();
            $table->date('subsidy_docs_submitted_at')->nullable();
            $table->date('subsidy_approved_at')->nullable();
            $table->date('subsidy_disbursed_at')->nullable();
            $table->timestamp('last_status_change_at')->nullable();
            $table->boolean('has_active_amc')->default(false);
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->decimal('advance_milestone_pct', 5, 2)->default(10.00);
            $table->decimal('delivery_milestone_pct', 5, 2)->default(70.00);
            $table->decimal('commissioning_milestone_pct', 5, 2)->default(20.00);
            $table->boolean('has_subsidy')->default(false);
            $table->decimal('central_subsidy', 12, 2)->default(0);
            $table->decimal('state_subsidy', 12, 2)->default(0);
            $table->decimal('net_cost', 12, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['brand', 'gst_rate', 'capacity_watts']);
        });

        Schema::table('installations', function (Blueprint $table) {
            $table->dropColumn([
                'subsidy_status', 'subsidy_registered_at', 'subsidy_docs_submitted_at',
                'subsidy_approved_at', 'subsidy_disbursed_at', 'last_status_change_at', 'has_active_amc'
            ]);
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn([
                'advance_milestone_pct', 'delivery_milestone_pct', 'commissioning_milestone_pct',
                'has_subsidy', 'central_subsidy', 'state_subsidy', 'net_cost'
            ]);
        });
    }
};
