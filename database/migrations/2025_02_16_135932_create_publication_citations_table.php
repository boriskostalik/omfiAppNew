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
        Schema::create('publication_citations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cited_id');
            $table->unsignedBigInteger('reference_id');
            $table->timestamp('created_at')->useCurrent();
            
            $table->foreign('cited_id')->references('id')->on('publications')->onDelete('cascade');
            $table->foreign('reference_id')->references('id')->on('publications')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publication_citations');
    }
};
