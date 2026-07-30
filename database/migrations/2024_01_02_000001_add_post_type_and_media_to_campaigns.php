<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            // 'post' = standard feed post, 'story' = story format
            $table->string('post_type')->default('post')->after('channel');
            // Public URL served to the Meta Graph API (can be a signed storage URL)
            $table->string('media_url', 2048)->nullable()->after('post_type');
            // Local storage path relative to storage/app/public
            $table->string('media_path')->nullable()->after('media_url');
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn(['post_type', 'media_url', 'media_path']);
        });
    }
};
