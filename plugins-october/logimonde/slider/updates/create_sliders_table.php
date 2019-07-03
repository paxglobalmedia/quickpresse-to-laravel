<?php namespace Logimonde\Slider\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateSlidersTable extends Migration
{

    public function up()
    {
        Schema::create('logimonde_slider_sliders', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('logimonde_slider_sliders');
    }

}
