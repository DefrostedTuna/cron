<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('monitor_id');
            $table->unsignedInteger('pair_id')->nullable();
            $table->string('type'); // Incoming / Outgoing
            $table->string('status'); // Success / Fail / Error
            $table->string('endpoint');
            $table->string('ip');
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
        Schema::dropIfExists('pings');
    }
}
