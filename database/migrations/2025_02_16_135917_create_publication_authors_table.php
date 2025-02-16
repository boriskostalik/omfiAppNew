<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('publication_authors', function (Blueprint $table) {
            $table->unsignedBigInteger('pub_id');
            $table->unsignedBigInteger('author_id');
            $table->unsignedInteger('rank')->default(1);
            $table->enum('is_editor', ['Y', 'N'])->default('N');
            $table->primary(['pub_id', 'author_id', 'is_editor']);
            
            $table->foreign('pub_id')->references('id')->on('publications')->onDelete('cascade');
            $table->foreign('author_id')->references('id')->on('authors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publication_authors');
    }
};
