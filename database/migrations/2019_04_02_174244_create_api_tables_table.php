
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Ongoingcloud\Laravelcrud\Models\Permission;
use Ongoingcloud\Laravelcrud\Models\Role;

class CreateApiTablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('api_tables', function (Blueprint $table) {
            $table->increments("id");
			$table->integer("apimodule_id")->unsigned();
			$table->string("name")->nullable();
			$table->string("type")->nullable();
			$table->string("validation")->nullable();
			$table->string("default")->nullable();
			$table->timestamps();
			
			$table->foreign("apimodule_id")->references("id")->on("api_modules")
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
