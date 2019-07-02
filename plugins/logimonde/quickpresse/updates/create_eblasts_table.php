<?php namespace Logimonde\Quickpresse\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateEblastsTable extends Migration
{
    public function up()
    {
        Schema::create('logimonde_quickpresse_eblasts', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('logimonde_quickpresse_eblasts');
    }
}
