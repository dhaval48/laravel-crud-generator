
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Ongoingcloud\Laravelcrud\Models\Permission;
use Ongoingcloud\Laravelcrud\Models\Role;

class CreateLanguageTransletDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('language_translet_details', function (Blueprint $table) {
            $table->increments("id");
            $table->integer('languagetranslet_id')->unsigned();
      			$table->string("value")->nullable();
      			$table->string("translation")->nullable();
      			$table->timestamps();
      			
      			$table->foreign("languagetranslet_id")->references("id")->on("language_translets")
                                  ->onUpdate("restrict")
                                  ->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
