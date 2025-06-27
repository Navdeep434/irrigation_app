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
       Schema::create('hardware_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('device_number');
            $table->json('valves')->nullable();
            $table->json('flow_sensors')->nullable();
            $table->boolean('temperature_sensor')->default(false);
            $table->timestamps();

            $table->foreign('device_number')
                ->references('device_number')
                ->on('devices')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hardware_statuses');
    }
};
