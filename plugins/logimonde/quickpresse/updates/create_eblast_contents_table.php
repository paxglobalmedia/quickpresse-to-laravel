<?php namespace Logimonde\Quickpresse\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateEblastContentsTable extends Migration
{
    public function up()
    {
        Schema::create('logimonde_quickpresse_eblast_contents', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('logimonde_quickpresse_eblast_contents');
    }
}
