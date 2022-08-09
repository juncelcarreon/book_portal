<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePodFakesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pod_fakes', function (Blueprint $table) {
            $table->id();
            $table->string('author');
            $table->string('book');
            $table->string('year');
            $table->string('month');
            $table->string('flag');
            $table->string('status');
            $table->string('format');
            $table->string('quantity');
            $table->string('price');
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
        Schema::dropIfExists('pod_fakes');
    }
}
