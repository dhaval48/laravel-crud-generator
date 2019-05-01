
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Create[CLASS_MODULE]Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $exist_module = DB::table('form_modules')->where('main_module','[EXIST_MODULE]')->first();
        if(empty($exist_module)) {

            $module_id = DB::table('form_modules')->insertGetId([
                            [DATA_MODULE]
                        ]);
            $data = [
                    [DATA_MODULE_TABLE]
                ];

            DB::table('module_tables')->insert($data);

            $values = [
                    [DATA_MODULE_INPUT]
                ];
            DB::table('module_inputs')->insert($values);
        }       
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
