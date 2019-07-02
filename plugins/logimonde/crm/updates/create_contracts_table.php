<?php namespace Logimonde\Crm\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateContractsTable extends Migration
{
    public function up()
    {
        Schema::create('logimonde_crm_contracts', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('logimonde_crm_contracts');
    }
}
