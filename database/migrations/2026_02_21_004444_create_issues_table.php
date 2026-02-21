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
    Schema::create('issues', function (Blueprint $table) {
        $table->id();
        $table->unsignedSmallInteger('year');
        $table->string('volume', 16)->nullable();
        $table->unsignedTinyInteger('number');
        $table->string('pdf_path', 512)->nullable();
        $table->date('published_at')->nullable();
        $table->timestamps();
        $table->unique(['year', 'volume', 'number'], 'issues_year_volume_number_unique');
        $table->index(['year', 'number']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    Schema::dropIfExists('issues');
    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
}
};
