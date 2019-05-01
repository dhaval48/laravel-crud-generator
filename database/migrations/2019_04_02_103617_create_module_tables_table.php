
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Ongoingcloud\Laravelcrud\Models\Permission;
use Ongoingcloud\Laravelcrud\Models\Role;

class CreateModuleTablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('module_tables', function (Blueprint $table) {
            $table->increments("id");
			$table->string("name")->nullable();
			$table->string("type")->nullable();
			$table->string("validation")->nullable();
			$table->string("default")->nullable();
			$table->integer("formmodule_id")->unsigned();
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
