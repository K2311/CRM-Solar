<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('site_surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('roof_area_sqft', 10, 2)->nullable();
            $table->string('roof_type')->nullable(); // concrete, metal_sheet, tiles, ground
            $table->string('shading_details')->nullable(); // none, partial, high
            $table->string('discom_name')->nullable();
            $table->decimal('sanctioned_load_kw', 8, 2)->nullable();
            $table->string('consumer_number')->nullable();
            $table->json('photos')->nullable();
            $table->text('notes')->nullable();
            $table->date('survey_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_surveys');
    }
};
