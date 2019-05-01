
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Ongoingcloud\Laravelcrud\Models\Permission;
use Ongoingcloud\Laravelcrud\Models\Role;

class CreatePermissionModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('permission_modules', function (Blueprint $table) {
            $table->increments("id");
			$table->string("name")->nullable();
            $table->integer("created_by")->nullable();
			$table->timestamps();
			$table->softDeletes();
        });

        DB::table('permission_modules')->insert([
            'name' => 'General',
        ]);

        DB::table('permission_modules')->insert([
            'name' => 'Authorization',
        ]);

        $check_exist = DB::table('modules')->where('name','Autolaravel')->first();
        $exist_group = DB::table('module_groups')->where('name','permissionmodule')->first();
        if(empty($exist_group)) {
            if(!empty($check_exist)) {
                $id = $check_exist->id;
            } else {
                $id = DB::table('modules')->insertGetId([
                    'name' => 'Autolaravel',
                    'description' => 'Autolaravel Modules',
                    'url' => 'permissionmodule',
                    'icon' => 'fa-database',
                    'order' => 2,
                ]);
            }   
            
            $module_groups=[
                    'name'=>'permissionmodule',
                    'display_name' => 'Permission',
                    'description'=>'Manage the permissionmodule',
                    'module_id'=> $id,
                    'status' => 1,
                    'icon' => 'fa-500px',
                    'permission' => 'list_permissionmodule',
                    'url' => 'permissionmodule',
                    'route' => 'permissionmodule.index'
                ];    
            $group_id = DB::table('module_groups')->insertGetId($module_groups);

            $permission = [
                [
                    'name'=>'store_permissionmodule',
                    'display_name'=>'Store permissionmodule',
                    'description'=>'Permission to store permissionmodule',
                    'module_group_id' => $group_id
                ],
                [
                    'name'=>'update_permissionmodule',
                    'display_name'=>'Update permissionmodule',
                    'description'=>'Permission to update permissionmodule',
                    'module_group_id' => $group_id
                ],
                [
                    'name'=>'list_permissionmodule',
                    'display_name'=>'List permissionmodule',
                    'description'=>'Permission to list permissionmodule',
                    'module_group_id' => $group_id
                ],
                [
                    'name'=>'delete_permissionmodule',
                    'display_name'=>'Delete permissionmodule',
                    'description'=>'Permission to delete permissionmodule',
                    'module_group_id' => $group_id
                ],
                [
                    'name'=>'only_permissionmodule',
                    'display_name'=>'Only If Creator',
                    'description'=>'Permission to only creator permissionmodule',
                    'module_group_id' => $group_id
                ],
                [
                    'name'=>'activity_permissionmodule',
                    'display_name'=>'Activity of Permission module',
                    'description'=>'Permission to activity permissionmodule',
                    'module_group_id' => $group_id
                ]
            ];
            DB::table('permissions')->insert($permission);

            $permission = Permission::where('module_group_id',$group_id)->get();
            $role = Role::find(1);       
            foreach($permission as $permission) {
                if($permission->name != "only_permissionmodule") {            
                    $role->permissions()->attach($role->id, ['permission_id' => $permission->id]);
                }
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
        
    }
}
