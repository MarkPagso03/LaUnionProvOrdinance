<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resolutions', function (Blueprint $table) {
            $table->id();                                        // primary key
            $table->string('resolution_number', 50)->unique();    // must be unique
            $table->string('title', 500);
            $table->string('subject', 255);                      // subject / topic of the record
            $table->date('date_passed')->index();
            $table->string('author_sponsor', 255);
            $table->text('summary')->nullable();                 // extra optional field
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resolutions');
    }
};
