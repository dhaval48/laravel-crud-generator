
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Ongoingcloud\Laravelcrud\Models\Permission;
use Ongoingcloud\Laravelcrud\Models\Role;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('roles', function (Blueprint $table) {
            $table->increments("id");
			$table->string("name");
            $table->integer("created_by")->nullable();
			$table->string("description")->nullable();
			$table->timestamps();
            $table->softDeletes();
			
        });


        Schema::create('role_permissions', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('role_id')->unsigned();
            $table->integer('permission_id')->unsigned();
            $table->timestamps();

            $table->foreign('role_id')->references('id')->on('roles')
                ->onUpdate('restrict')
                ->onDelete('cascade');

        });

        DB::table('roles')->insert([
            'name' => 'Administrator',
            'description' => 'Has All Permissions',
        ]);

        $i = 2;
        $permission = [
        // Use Module Permission = 1
            [
                'name'=>'store_role',
                'display_name'=>'Store Role',
                'description'=>'Permission to store role',
                'module_group_id' => $i
            ],
            [
                'name'=>'update_role',
                'display_name'=>'Update Role',
                'description'=>'Permission to update role',
                'module_group_id' => $i
            ],
            [
                'name'=>'list_role',
                'display_name'=>'List Role',
                'description'=>'Permission to list role',
                'module_group_id' => $i
            ],
            [
                'name'=>'delete_role',
                'display_name'=>'Delete Role',
                'description'=>'Permission to delete role',
                'module_group_id' => $i
            ],
            [
                'name'=>'only_role',
                'display_name'=>'Only If Creator',
                'description'=>'Permission to only creator role',
                'module_group_id' => $i
            ],
            [
                'name'=>'activity_role',
                'display_name'=>'Activity of Role',
                'description'=>'Permission to activity role',
                'module_group_id' => $i
            ]
        ];
        DB::table('permissions')->insert($permission);

        $permission = Permission::where('module_group_id',$i)->get();
        $role = Role::find(1);       
        foreach($permission as $permission) {
            if($permission->name != "only_role") {
                $role->permissions()->attach($role->id, ['permission_id' => $permission->id]);
            }            
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
