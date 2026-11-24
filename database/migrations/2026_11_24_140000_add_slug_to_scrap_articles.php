<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // Friendly, stable URLs for the public blog (/blog/<slug>) - good for search engines
        Schema::table('scrap_articles', function (Blueprint $table) {
            $table->string('slug', 190)->nullable()->unique()->after('title');
        });

        foreach (DB::table('scrap_articles')->whereNull('slug')->get() as $row) {
            $base = Str::slug($row->title) ?: 'article';
            $slug = $base;
            $i = 2;
            while (DB::table('scrap_articles')->where('slug', $slug)->exists()) {
                $slug = $base . '-' . $i++;
            }
            DB::table('scrap_articles')->where('id', $row->id)->update(['slug' => $slug]);
        }
    }

    public function down(): void
    {
        Schema::table('scrap_articles', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
