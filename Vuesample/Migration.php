
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Ongoingcloud\Laravelcrud\Models\Permission;
use Ongoingcloud\Laravelcrud\Models\Role;

class Create[CLASS_MODULE]Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('[TABLE_NAME]', function (Blueprint $table) {
            [TABLE_FIELDS]
            $table->integer("created_by")->nullable();
        });


        $check_exist = DB::table('modules')->where('name','[PAMODULE]')->first();
        $exist_group = DB::table('module_groups')->where('name','[MODULE]')->first();
        if(empty($exist_group)) {
            if(!empty($check_exist)) {
                $id = $check_exist->id;
            } else {
                $module = \DB::table('modules')->orderBy('id', 'DESC')->first();

                $id = DB::table('modules')->insertGetId([
                    'name' => '[PMODULE]',
                    'description' => 'Manage [PMODULE]',
                    'url' => '[LAMODULE]',
                    'icon' => 'fa-database',
                    'order' => $module->order+1,
                ]);
            }   
            
            $module_groups=[
                    'name'=>'[MODULE]',
                    'display_name' => '[ULABEL]',
                    'description'=>'Manage the [ULABEL]',
                    'module_id'=> $id,
                    'status' => 1,
                    'icon' => 'fa-500px',
                    'permission' => 'list_[LMODULE]',
                    'url' => '[MODULE]',
                    'route' => '[LMODULE].index'
                ];    
            $group_id = DB::table('module_groups')->insertGetId($module_groups);

            $permission = [
                [
                    'name'=>'store_[LMODULE]',
                    'display_name'=>'Store [ULABEL]',
                    'description'=>'Permission to store [LMODULE]',
                    'module_group_id' => $group_id
                ],
                [
                    'name'=>'update_[LMODULE]',
                    'display_name'=>'Update [ULABEL]',
                    'description'=>'Permission to update [LMODULE]',
                    'module_group_id' => $group_id
                ],
                [
                    'name'=>'list_[LMODULE]',
                    'display_name'=>'List [ULABEL]',
                    'description'=>'Permission to list [LMODULE]',
                    'module_group_id' => $group_id
                ],
                [
                    'name'=>'delete_[LMODULE]',
                    'display_name'=>'Delete [ULABEL]',
                    'description'=>'Permission to delete [LMODULE]',
                    'module_group_id' => $group_id
                ],
                [
                    'name'=>'only_[LMODULE]',
                    'display_name'=>'Only If Creator',
                    'description'=>'Permission to only creator [LMODULE]',
                    'module_group_id' => $group_id
                ],
                [
                    'name'=>'activity_[LMODULE]',
                    'display_name'=>'Activity of [ULABEL]',
                    'description'=>'Permission to activity [LMODULE]',
                    'module_group_id' => $group_id
                ]
            ];
            DB::table('permissions')->insert($permission);

            $permission = Permission::where('module_group_id',$group_id)->get();
            $role = Role::find(1);       
            foreach($permission as $permission) {
                if($permission->name != "only_[LMODULE]") {            
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
        //
    }
}
