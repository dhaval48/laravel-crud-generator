<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModuleGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_groups', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name', 255);
            $table->string('display_name', 255)->nullable();
            $table->string('description', 255);
            $table->integer('module_id')->nullable();
            $table->tinyinteger('status')->default(0);
            $table->string('icon');
            $table->string('permission');
            $table->string('url');
            $table->string('route');
            $table->integer("order")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        $i=1;
        $module_groups = [
        // Authorization Module = 1
            [
                'name'=>'User',
                'display_name'=>'User',
                'description'=>'User Manage Module',
                'module_id'=> $i,
                'permission' => 'list_user',
                'status' => 1,
                'url' => 'user',
                'route' => 'user.index',
                'icon' => 'fa-fw fa-users'
            ],

            [
                'name'=>'Role',
                'display_name'=>'Role',
                'description'=>'Roles Manage modules',
                'module_id'=> $i,
                'permission' => 'list_role',
                'status' => 1,
                'url' => 'role',
                'route' => 'role.index',
                'icon' => 'fa-fw fa-user-secret'
            ]
        ];

        DB::table('module_groups')->insert($module_groups);
        $i++;
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('module_groups');
    }
}
