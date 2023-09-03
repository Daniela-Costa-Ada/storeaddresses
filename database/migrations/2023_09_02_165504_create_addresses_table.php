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
        Schema::create('addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('foreign_id')->unique(); 
            //$table->string('foreign_table', 200)->unique();
            $table->string('postal_code');
            $table->string('street_number', 200);
            $table->string('state');
            $table->string('city', 200);
            $table->string('sublocality', 200);
            $table->string('street', 200);
            $table->string('complement', 200)->default('');

            $table->foreign('foreign_id')->references('id')->on('stores')->onDelete('CASCADE')->onUpdate('CASCADE');
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
