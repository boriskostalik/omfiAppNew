<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publication_keywords', function (Blueprint $table) {
            $table->foreignId('publication_id')->constrained('publications')->cascadeOnDelete();
            $table->foreignId('keyword_id')->constrained('keywords')->cascadeOnDelete();
            $table->primary(['publication_id', 'keyword_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publication_keywords');
    }
};
