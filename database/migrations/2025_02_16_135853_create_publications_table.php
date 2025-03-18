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
            $table->string('pub_type', 255)->default('');
            $table->enum('type', ['Article','Book','Booklet','Inbook','Incollection','Inproceedings','Manual','Mastersthesis','Misc','Phdthesis','Proceedings','Techreport','Unpublished'])->nullable();
            $table->tinyInteger('survey')->default(0);
            $table->integer('mark')->default(5);
            $table->string('series', 64)->default('');
            $table->string('volume', 16)->default('');
            $table->string('publisher', 127)->default('');
            $table->string('location', 127)->default('');
            $table->string('issn', 32)->default('');
            $table->string('isbn', 32)->default('');
            $table->string('firstpage', 10)->default('0');
            $table->string('lastpage', 10)->default('0');
            $table->string('journal', 255)->default('');
            $table->string('booktitle', 255)->default('');
            $table->string('number', 255)->default('1');
            $table->string('institution', 255)->default('');
            $table->string('address', 255)->default('');
            $table->string('chapter', 10)->default('0');
            $table->string('edition', 255)->default('');
            $table->string('howpublished', 255)->default('');
            $table->string('month', 255)->default('');
            $table->string('organization', 255)->default('');
            $table->string('school', 255)->default('');
            $table->text('note');
            $table->text('keywords');
            $table->text('abstract');
            $table->string('url', 255)->default('');
            $table->string('doi', 255)->default('');
            $table->string('crossref', 255);
            $table->string('namekey', 255);
            $table->text('userfields');
            $table->enum('specialchars', ['FALSE', 'TRUE'])->default('FALSE');
            $table->string('cleanjournal', 255)->default('');
            $table->string('cleantitle', 255)->default('');
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
