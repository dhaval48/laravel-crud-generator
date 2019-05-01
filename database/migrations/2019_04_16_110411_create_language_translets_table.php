
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Ongoingcloud\Laravelcrud\Models\Permission;
use Ongoingcloud\Laravelcrud\Models\Role;

class CreateLanguageTransletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('language_translets', function (Blueprint $table) {
            $table->increments("id");
			$table->tinyinteger("status")->default("1");
			$table->string("locale")->nullable();
			$table->timestamps();
			$table->softDeletes();
		
            $table->integer("created_by")->nullable();
        });


        $check_exist = DB::table('modules')->where('name','General')->first();
        $exist_group = DB::table('module_groups')->where('name','languagetranslet')->first();
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
                    'name'=>'languagetranslet',
                    'display_name' => 'Language Translet',
                    'description'=>'Manage the Language Translet',
                    'module_id'=> $id,
                    'status' => 1,
                    'icon' => 'fa-500px',
                    'permission' => 'list_languagetranslet',
                    'url' => 'languagetranslet',
                    'route' => 'languagetranslet.index'
                ];    
            $group_id = DB::table('module_groups')->insertGetId($module_groups);

            $permission = [
                [
                    'name'=>'store_languagetranslet',
                    'display_name'=>'Store Language Translet',
                    'description'=>'Permission to store languagetranslet',
                    'module_group_id' => $group_id
                ],
                [
                    'name'=>'update_languagetranslet',
                    'display_name'=>'Update Language Translet',
                    'description'=>'Permission to update languagetranslet',
                    'module_group_id' => $group_id
                ],
                [
                    'name'=>'list_languagetranslet',
                    'display_name'=>'List Language Translet',
                    'description'=>'Permission to list languagetranslet',
                    'module_group_id' => $group_id
                ],
                [
                    'name'=>'delete_languagetranslet',
                    'display_name'=>'Delete Language Translet',
                    'description'=>'Permission to delete languagetranslet',
                    'module_group_id' => $group_id
                ],
                [
                    'name'=>'only_languagetranslet',
                    'display_name'=>'Only If Creator',
                    'description'=>'Permission to only creator languagetranslet',
                    'module_group_id' => $group_id
                ],
                [
                    'name'=>'activity_languagetranslet',
                    'display_name'=>'Activity of Language Translet',
                    'description'=>'Permission to activity languagetranslet',
                    'module_group_id' => $group_id
                ]
            ];
            DB::table('permissions')->insert($permission);

            $permission = Permission::where('module_group_id',$group_id)->get();
            $role = Role::find(1);       
            foreach($permission as $permission) {
                if($permission->name != "only_languagetranslet") {            
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
