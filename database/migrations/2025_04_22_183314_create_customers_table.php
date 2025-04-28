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

            // Link to the users table (assuming user_id exists in users table)
            $table->unsignedBigInteger('user_id')->unique();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Unique identifier for customers
            $table->string('uid')->unique()->nullable();
            $table->year('uid_year')->nullable();
            $table->unsignedInteger('uid_number')->nullable();

            // Customer details
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique(); // Make email unique
            $table->string('country_code', 5)->nullable();
            $table->string('contact_number', 15)->nullable();
            $table->string('gender')->nullable();
            $table->date('dob')->nullable();

            // Customer status (active/inactive)
            $table->enum('status', ['active', 'inactive', 'blocked'])->default('active');

            // Block status field for easier management (optional)
            $table->boolean('is_blocked')->default(false);

            // Verification status field
            $table->boolean('is_verified')->default(false); // Customer verification status (default to false)

            // Add soft deletes column
            $table->softDeletes(); // This adds the 'deleted_at' column

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
