<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('marketing_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('channel'); // sms, whatsapp, email, facebook, instagram
            $table->string('subject')->nullable(); // for email
            $table->text('body');
            $table->json('variables')->nullable(); // e.g. ["name","phone"]
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('channel'); // sms, whatsapp, email, facebook, instagram
            $table->string('status')->default('draft'); // draft, scheduled, sending, sent, failed
            $table->string('subject')->nullable();
            $table->text('body');
            $table->string('segment')->default('all'); // all, leads, customers, custom
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->integer('sent_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->integer('total_contacts')->default(0);
            $table->timestamps();
        });

        Schema::create('campaign_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->string('contact_type'); // customer, lead
            $table->unsignedBigInteger('contact_id');
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('status')->default('pending'); // pending, sent, failed, bounced
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
            $table->index(['contact_type', 'contact_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_contacts');
        Schema::dropIfExists('campaigns');
        Schema::dropIfExists('marketing_templates');
    }
};
