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
            $table->foreignId('institute_id')->nullable()->constrained('institutes')->nullOnDelete();
            $table->string('cleanname', 500)->storedAs("TRIM(CONCAT(IF(von IS NOT NULL AND von != '', CONCAT(von, ' '), ''), surname, IF(firstname IS NOT NULL AND firstname != '', CONCAT(', ', firstname), '')))");
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
