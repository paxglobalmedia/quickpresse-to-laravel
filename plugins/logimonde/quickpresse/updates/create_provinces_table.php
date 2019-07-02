<?php namespace Logimonde\Webinar\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateProvincesTable extends Migration
{
    public function up()
    {
        Schema::create('logimonde_webinar_provinces', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('logimonde_webinar_provinces');
    }
}
