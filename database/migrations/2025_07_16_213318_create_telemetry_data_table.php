<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telemetry_data', function (Blueprint $table) {
            $table->id();
            $table->float('temperature');
            $table->float('humidity');
            $table->dateTime('measured_at');
            $table->enum('threshold_temp_type', ['normal', 'warning', 'critical']);
            $table->enum('threshold_humidity_type', ['normal', 'warning', 'critical']);
            $table->string('thresholds_defined');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('telemetry_data');
    }
};
