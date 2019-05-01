
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Ongoingcloud\Laravelcrud\Models\Permission;
use Ongoingcloud\Laravelcrud\Models\Role;

class Create[CLASS_MODULE]Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('[TABLE_NAME]', function (Blueprint $table) {
            [TABLE_FIELDS]
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
