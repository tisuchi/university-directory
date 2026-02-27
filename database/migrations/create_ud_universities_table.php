<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ud_universities', function (Blueprint $table) {
            $table->id();
            $table->string('wikidata_id')->nullable()->unique();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->string('slug')->unique();
            $table->string('country_code', 2)->index();
            $table->string('type');
            $table->string('official_website')->nullable();
            $table->json('aliases')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['country_code', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ud_universities');
    }
};
