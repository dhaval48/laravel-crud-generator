
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Ongoingcloud\Laravelcrud\Models\Permission;
use Ongoingcloud\Laravelcrud\Models\Role;

class CreateFormModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('form_modules', function (Blueprint $table) {
            $table->increments("id");
			$table->string("parent_form")->nullable();
			$table->integer("created_by")->nullable();
            $table->string("parent_module")->nullable();
			$table->string("main_module")->nullable();
            $table->string("table_name")->nullable();
			$table->timestamps();
			$table->softDeletes();
        });


        $check_exist = DB::table('modules')->where('name','Autolaravel')->first();
        $exist_group = DB::table('module_groups')->where('name','formmodule')->first();
        if(empty($exist_group)) {
            if(!empty($check_exist)) {
                $id = $check_exist->id;
            } else {
                $id = DB::table('modules')->insertGetId([
                    'name' => 'Autolaravel',
                    'description' => 'Autolaravel Modules',
                    'url' => 'autolaravel',
                    'icon' => 'fa-database',
                    'order' => 2,
                ]);
            }   

            $module_groups=[
                    'name'=>'gridmodule',
                    'display_name' => 'Grid',
                    'description'=>'Manage the gridmodule',
                    'module_id'=> $id,
                    'status' => 1,
                    'icon' => 'fa-500px',
                    'permission' => 'list_formmodule',
                    'url' => 'gridmodule',
                    'route' => 'gridmodule.index'
                ];

            $group_id = DB::table('module_groups')->insertGetId($module_groups);
            
            $module_groups=[
                    'name'=>'formmodule',
                    'display_name' => 'Form',
                    'description'=>'Manage the formmodule',
                    'module_id'=> $id,
                    'status' => 1,
                    'icon' => 'fa-500px',
                    'permission' => 'list_formmodule',
                    'url' => 'formmodule',
                    'route' => 'formmodule.index'
                ];

            $group_id = DB::table('module_groups')->insertGetId($module_groups);

            $permission = [
                [
                    'name'=>'store_formmodule',
                    'display_name'=>'Store formmodule',
                    'description'=>'Permission to store formmodule',
                    'module_group_id' => $group_id
                ],
                [
                    'name'=>'update_formmodule',
                    'display_name'=>'Update formmodule',
                    'description'=>'Permission to update formmodule',
                    'module_group_id' => $group_id
                ],
                [
                    'name'=>'list_formmodule',
                    'display_name'=>'List formmodule',
                    'description'=>'Permission to list formmodule',
                    'module_group_id' => $group_id
                ],
                [
                    'name'=>'delete_formmodule',
                    'display_name'=>'Delete formmodule',
                    'description'=>'Permission to delete formmodule',
                    'module_group_id' => $group_id
                ],
                [
                    'name'=>'only_formmodule',
                    'display_name'=>'Only If Creator',
                    'description'=>'Permission to only creator formmodule',
                    'module_group_id' => $group_id
                ],
                [
                    'name'=>'activity_formmodule',
                    'display_name'=>'Activity of Form module',
                    'description'=>'Permission to activity formmodule',
                    'module_group_id' => $group_id
                ]
            ];
            DB::table('permissions')->insert($permission);

            $permission = Permission::where('module_group_id',$group_id)->get();
            $role = Role::find(1);       
            foreach($permission as $permission) {  
                if($permission->name != "only_formmodule") {          
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
