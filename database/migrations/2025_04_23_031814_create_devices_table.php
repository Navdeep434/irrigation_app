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
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
        
            $table->integer('total_valves')->default(0);
            $table->integer('total_flow_sensors')->default(0);
            $table->integer('total_water_temp_sensors')->default(1);
        
            $table->boolean('in_repair')->default(false);
            $table->boolean('is_blocked')->default(false);
        
            $table->string('status')->default('inactive');
            $table->timestamps();
            $table->softDeletes();
        
            // ← NEW: record who deleted this row
            $table->unsignedBigInteger('deleted_by')->nullable()->after('deleted_at');
            $table->foreign('deleted_by')
                  ->references('id')->on('users')
                  ->onDelete('set null');
        
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('set null');
            $table->foreign('customer_id')
                  ->references('id')->on('customers')
                  ->onDelete('set null');
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
