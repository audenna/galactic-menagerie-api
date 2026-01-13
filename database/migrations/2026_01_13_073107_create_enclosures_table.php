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
        Schema::create('enclosures', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // Volcanic, Tundra, Jungle
            $table->unsignedInteger('capacity')->comment('This represents non-negative whole numbers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enclosures');
    }
};
