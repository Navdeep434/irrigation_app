<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('device_readings', function (Blueprint $table) {
            $table->id();
            $table->string('device_number');

            $table->float('flow_rate1')->nullable();
            $table->float('flow_rate2')->nullable();
            $table->float('flow_rate3')->nullable();
            $table->float('flow_rate4')->nullable();
            $table->float('flow_rate5')->nullable();
            $table->float('flow_rate6')->nullable();
            $table->float('flow_rate7')->nullable();
            $table->float('flow_rate8')->nullable();

            $table->float('temperature')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_readings');
    }
};

