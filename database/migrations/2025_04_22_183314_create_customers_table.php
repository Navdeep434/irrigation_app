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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
    
            $table->unsignedBigInteger('user_id')->unique();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    
            $table->string('uid')->unique()->nullable();
            $table->year('uid_year')->nullable();
            $table->unsignedInteger('uid_number')->nullable();
    
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('country_code', 5)->nullable();
            $table->string('contact_number', 15)->nullable();
            $table->string('gender')->nullable();
            $table->date('dob')->nullable();
            $table->string('status')->default('active');
    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
