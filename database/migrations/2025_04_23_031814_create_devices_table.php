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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_number')->unique();

            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            $table->foreignId('customer_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            $table->unsignedInteger('total_valves')->default(0);
            $table->unsignedInteger('total_flow_sensors')->default(0);
            $table->unsignedInteger('total_water_temp_sensors')->default(1);

            $table->boolean('in_repair')->default(false);
            $table->boolean('is_blocked')->default(false);

            $table->string('status')->default('inactive');
            $table->timestamp('last_seen')->nullable();
            $table->string('live_status')->default('offline');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('unassigned_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Record who deleted this row
            $table->foreignId('deleted_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->after('deleted_at');

            // Index for faster lookups by user and status
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
