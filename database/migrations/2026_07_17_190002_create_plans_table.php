<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->decimal('price', 8, 2)->default(0);
            $table->integer('user_limit')->default(3);
            $table->integer('lead_limit')->default(15);
            $table->boolean('whatsapp_templates')->default(false);
            $table->boolean('branding')->default(false);
            $table->timestamps();
        });

        // Seed default plans immediately
        \DB::table('plans')->insert([
            [
                'slug' => 'demo',
                'name' => 'Demo Trial Plan',
                'price' => 0.00,
                'user_limit' => 3,
                'lead_limit' => 15,
                'whatsapp_templates' => false,
                'branding' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'pro',
                'name' => 'Pro Solar Plan',
                'price' => 49.00,
                'user_limit' => 10,
                'lead_limit' => 500,
                'whatsapp_templates' => true,
                'branding' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'enterprise',
                'name' => 'Enterprise Solar Plan',
                'price' => 149.00,
                'user_limit' => 999,
                'lead_limit' => 99999,
                'whatsapp_templates' => true,
                'branding' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
