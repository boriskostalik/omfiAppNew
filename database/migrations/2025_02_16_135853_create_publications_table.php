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
        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('actualyear')->nullable();
            $table->text('title');
            $table->text('title_eng')->nullable();
            $table->enum('type', ['Article','Book','Booklet','Inbook','Incollection','Inproceedings','Manual','Mastersthesis','Misc','Phdthesis','Proceedings','Techreport','Unpublished'])->nullable();
            $table->string('isbn', 32)->nullable();
            $table->string('firstpage', 10)->default('0');
            $table->string('lastpage', 10)->default('0');
            $table->text('abstract')->nullable();
            $table->string('doi', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publications');
    }
};
