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
        Schema::create('authors', function (Blueprint $table) {
            $table->id('id');
            $table->string('surname');
            $table->string('von')->nullable();
            $table->string('firstname');
            $table->string('email')->nullable();
            $table->string('url')->nullable();
            $table->string('institute')->nullable();
            $table->boolean('specialchars')->default('0')->nullable();
            $table->string('cleanname')->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authors');
    }
};
