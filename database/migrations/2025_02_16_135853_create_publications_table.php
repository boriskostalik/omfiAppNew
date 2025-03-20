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
            $table->unsignedBigInteger('entered_by')->default(0);
            $table->string('year', 12)->default('0000');
            $table->string('actualyear', 12)->default('0000');
            $table->text('title');
            $table->text('title_eng')->nullable();
            $table->string('mesc', 50)->nullable();
            $table->string('bibtex_id', 255);
            $table->string('pub_type', 255)->nullable();
            $table->enum('type', ['Article','Book','Booklet','Inbook','Incollection','Inproceedings','Manual','Mastersthesis','Misc','Phdthesis','Proceedings','Techreport','Unpublished'])->nullable();
            $table->tinyInteger('survey')->default(0);
            $table->integer('mark')->default(5);
            $table->string('series', 64)->nullable();
            $table->string('volume', 16)->nullable();
            $table->string('publisher', 127)->nullable();
            $table->string('location', 127)->nullable();
            $table->string('issn', 32)->nullable();
            $table->string('isbn', 32)->nullable();
            $table->string('firstpage', 10)->default('0');
            $table->string('lastpage', 10)->default('0');
            $table->string('journal', 255)->nullable();
            $table->string('booktitle', 255)->nullable();
            $table->string('number', 255)->default('1');
            $table->string('institution', 255)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('chapter', 10)->default('0');
            $table->string('edition', 255)->nullable();
            $table->string('howpublished', 255)->nullable();
            $table->string('month', 255)->nullable();
            $table->string('organization', 255)->nullable();
            $table->string('school', 255)->nullable();
            $table->text('note')->nullable();
            $table->text('keywords')->nullable();
            $table->text('abstract')->nullable();
            $table->string('url', 255)->nullable();
            $table->string('doi', 255)->nullable();
            $table->string('crossref', 255)->nullable();
            $table->string('namekey', 255)->nullable();
            $table->text('userfields')->nullable();
            $table->enum('specialchars', ['FALSE', 'TRUE'])->default('FALSE');
            $table->string('cleanjournal', 255)->nullable();
            $table->string('cleantitle', 255)->nullable();
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
