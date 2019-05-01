
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Ongoingcloud\Laravelcrud\Models\Permission;
use Ongoingcloud\Laravelcrud\Models\Role;

class CreateApiModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('api_modules', function (Blueprint $table) {
            $table->increments("id");
			$table->string("parent_form")->nullable();
            $table->integer("created_by")->nullable();
			$table->string("parent_module")->nullable();
			$table->string("main_module")->nullable();
			$table->tinyinteger("is_model")->nullable();
            $table->tinyinteger("is_public")->nullable();
			$table->string("table_name")->nullable();
			$table->timestamps();
			$table->softDeletes();
			
        });


        $check_exist = DB::table('modules')->where('name','Autolaravel')->first();
        $exist_group = DB::table('module_groups')->where('name','apimodule')->first();
        if(empty($exist_group)) {
            if(!empty($check_exist)) {
                $id = $check_exist->id;
            } else {
                $id = DB::table('modules')->insertGetId([
                    'name' => 'Autolaravel',
                    'description' => 'Autolaravel Modules',
                    'url' => 'autolaravel',
                    'icon' => 'fa-database',
                    'order' => 1,
                ]);
            }   
            
            $module_groups=[
                    'name'=>'apimodule',
                    'display_name' => 'Api',
                    'description'=>'Manage the apimodule',
                    'module_id'=> $id,
                    'status' => 1,
                    'icon' => 'fa-500px',
                    'permission' => 'list_apimodule',
                    'url' => 'apimodule',
                    'route' => 'apimodule.index'
                ];    
            $group_id = DB::table('module_groups')->insertGetId($module_groups);

            $permission = [
                [
                    'name'=>'store_apimodule',
                    'display_name'=>'Store apimodule',
                    'description'=>'Permission to store apimodule',
                    'module_group_id' => $group_id
                ],
                [
                    'name'=>'update_apimodule',
                    'display_name'=>'Update apimodule',
                    'description'=>'Permission to update apimodule',
                    'module_group_id' => $group_id
                ],
                [
                    'name'=>'list_apimodule',
                    'display_name'=>'List apimodule',
                    'description'=>'Permission to list apimodule',
                    'module_group_id' => $group_id
                ],
                [
                    'name'=>'delete_apimodule',
                    'display_name'=>'Delete apimodule',
                    'description'=>'Permission to delete apimodule',
                    'module_group_id' => $group_id
                ],
                [
                    'name'=>'only_apimodule',
                    'display_name'=>'Only If Creator',
                    'description'=>'Permission to only creator apimodule',
                    'module_group_id' => $group_id
                ],
                [
                    'name'=>'activity_apimodule',
                    'display_name'=>'Activity of Api module',
                    'description'=>'Permission to activity apimodule',
                    'module_group_id' => $group_id
                ]
            ];
            DB::table('permissions')->insert($permission);

            $permission = Permission::where('module_group_id',$group_id)->get();
            $role = Role::find(1);       
            foreach($permission as $permission) { 
                if($permission->name != "only_apimodule") {           
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
