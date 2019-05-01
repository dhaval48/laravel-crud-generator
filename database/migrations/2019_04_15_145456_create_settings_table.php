
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Ongoingcloud\Laravelcrud\Models\Permission;
use Ongoingcloud\Laravelcrud\Models\Role;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('settings', function (Blueprint $table) {
            $table->increments("id");
			$table->tinyinteger("enable_registration")->nullable();
			$table->timestamps();
			$table->softDeletes();
			
			
            $table->integer("created_by")->nullable();
        });


        DB::table('settings')->insertGetId([
                            
                'enable_registration' => 1,
                
            ]);

        $check_exist = DB::table('modules')->where('name','General')->first();
        $exist_group = DB::table('module_groups')->where('name','setting')->first();
        if(empty($exist_group)) {
            if(!empty($check_exist)) {
                $id = $check_exist->id;
            } else {
                $module = \DB::table('modules')->orderBy('id', 'DESC')->first();

                $id = DB::table('modules')->insertGetId([
                    'name' => 'General',
                    'description' => 'Manage General',
                    'url' => 'general',
                    'icon' => 'fa-database',
                    'order' => $module->order+1,
                ]);
            }   
            
            $module_groups=[
                    'name'=>'setting',
                    'display_name' => 'Setting',
                    'description'=>'Manage the Setting',
                    'module_id'=> $id,
                    'status' => 0,
                    'icon' => 'fa-500px',
                    'permission' => 'list_setting',
                    'url' => 'setting',
                    'route' => 'setting.index'
                ];    
            $group_id = DB::table('module_groups')->insertGetId($module_groups);

            $permission = [
                [
                    'name'=>'store_setting',
                    'display_name'=>'Store Setting',
                    'description'=>'Permission to store setting',
                    'module_group_id' => $group_id
                ],
                [
                    'name'=>'update_setting',
                    'display_name'=>'Update Setting',
                    'description'=>'Permission to update setting',
                    'module_group_id' => $group_id
                ],
                [
                    'name'=>'list_setting',
                    'display_name'=>'List Setting',
                    'description'=>'Permission to list setting',
                    'module_group_id' => $group_id
                ],
                [
                    'name'=>'delete_setting',
                    'display_name'=>'Delete Setting',
                    'description'=>'Permission to delete setting',
                    'module_group_id' => $group_id
                ],
                [
                    'name'=>'only_setting',
                    'display_name'=>'Only If Creator',
                    'description'=>'Permission to only creator setting',
                    'module_group_id' => $group_id
                ],
                [
                    'name'=>'activity_setting',
                    'display_name'=>'Activity of Setting',
                    'description'=>'Permission to activity setting',
                    'module_group_id' => $group_id
                ]
            ];
            DB::table('permissions')->insert($permission);

            $permission = Permission::where('module_group_id',$group_id)->get();
            $role = Role::find(1);       
            foreach($permission as $permission) {
                if($permission->name != "only_setting") {            
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
