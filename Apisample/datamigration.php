
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

        $exist_module = DB::table('api_modules')->where('main_module','[EXIST_MODULE]')->first();
        
        if(empty($exist_module)) {
            $module_id = DB::table('api_modules')->insertGetId([
                            [DATA_MODULE]
                        ]);
            $data = [
                    [DATA_MODULE_TABLE]
                ];

            DB::table('api_tables')->insert($data);
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
