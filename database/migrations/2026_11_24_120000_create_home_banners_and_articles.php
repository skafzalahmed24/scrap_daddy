<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Scrolling banners of the Home sections: Sell bulk scrap, Scrap your vehicle, Offers
        Schema::create('home_banners', function (Blueprint $table) {
            $table->id();
            $table->string('section_key', 40);              // sell_bulk | scrap_vehicle | offers
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('image');
            $table->string('link_type', 20)->default('none'); // none | bulk_enquiry | pickup | vehicle | url
            $table->string('link_value', 500)->nullable();    // only for link_type = url
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
            $table->index(['section_key', 'is_active', 'sort']);
        });

        // "Know your scrap" articles, each with a cover and a gallery of banner images
        Schema::create('scrap_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('summary', 300)->nullable();
            $table->longText('content')->nullable();
            $table->string('cover_image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('scrap_article_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scrap_article_id')->constrained('scrap_articles')->cascadeOnDelete();
            $table->string('image');
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scrap_article_images');
        Schema::dropIfExists('scrap_articles');
        Schema::dropIfExists('home_banners');
    }
};
