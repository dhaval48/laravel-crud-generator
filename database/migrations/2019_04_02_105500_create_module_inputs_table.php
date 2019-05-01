
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Ongoingcloud\Laravelcrud\Models\Permission;
use Ongoingcloud\Laravelcrud\Models\Role;

class CreateModuleInputsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('module_inputs', function (Blueprint $table) {
            $table->increments("id");
			$table->integer("formmodule_id")->unsigned();
            $table->tinyinteger("visible")->nullable();
            $table->string("input_name")->nullable();
            $table->string("db_name")->nullable();
			$table->string("input_type")->nullable();
			$table->string("key")->nullable();
			$table->string("value")->nullable();
			$table->string("table")->nullable();
			$table->timestamps();
			
			$table->foreign("formmodule_id")->references("id")->on("form_modules")
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
