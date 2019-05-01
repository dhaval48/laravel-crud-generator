<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name', 255);
            $table->string('description', 255);
            $table->string('url');
            $table->string('icon');
            $table->integer("order")->nullable();
            $table->tinyinteger('is_visible')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        $modules=[
            
            [
                'name'=>'Authorization', 
                'description'=>'Module to Manage Authentication Process',
                'url' => 'control',
                'icon' => 'fa-fw fa-terminal',
                'order' => 2,
            ],

            [
                'name' => 'Autolaravel',
                'description' => 'Autolaravel Modules',
                'url' => 'autolaravel',
                'icon' => 'fa-database',
                'order' => 1,
            ],
            
            [
                'name' => 'General',
                'description' => 'Manage General',
                'url' => 'general',
                'icon' => 'fa-database',
                'order' => 3,
            ]
        ];
        DB::table('modules')->insert($modules);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modules');
    }
}
