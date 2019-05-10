<?php

namespace Ongoingcloud\Laravelcrud\General;
use Ongoingcloud\Laravelcrud\Models\ModuleGroup;
use Ongoingcloud\Laravelcrud\Models\Permission;
use Ongoingcloud\Laravelcrud\Models\Role;
use DB;

class HandlePermission {
 
	public static function getSideLinks() {
		$result = ['modules' => [], 'permissions' => []];

		$modules = DB::table('modules')->where('is_visible',1)->where('name', "!=", "Setting")->orderBy('order','ASC')->get();

		foreach ($modules as $key => $module) {
    		$module_name = strtolower($module->name);
			
			// if(HandlePermission::moduleAccess($module->id)) {

				$result['module_groups'][$module_name] = DB::table('module_groups')->where('module_id',$module->id)
					// ->whereNotIn('name',["Grades","Locations","Units","Section"])
					->where('status',1)->orderBy('order',"ASC")->get();

				$mgroup_ids = DB::table('module_groups')->where('module_id',$module->id)->where('status',1)->pluck('id');
				
		    	foreach(\Auth::user()->with('roles')->where('id', \Auth::user()->id)->get()[0]->roles as $role) {

						$permission_roles = Role::with('permissions')->find($role->id);
					
					if(count($result['module_groups'][$module_name])>0) {

						if(!empty($permission_roles->permissions()->whereIn('module_group_id',$mgroup_ids)->first())) {
							foreach ($permission_roles->permissions as $value) {
								
								$result['modules'][$module_name] = $module;
								$result['permissions'][] = $value->name;

							}
						}
					} else {
						$result['modules'][$module_name] = $module;
						$result['permissions'][] = $value->name;
					}
				}
			// }
		}
		return $result;
	}

	public static function extraSideLink() {
		$result['module_groups'] = DB::table('module_groups')
			->where('status',1)->wherenull('module_id')->orderBy('order',"ASC")->get();
		return $result;
	}

	public static function authorize($permission, $more_permissions = false) {
		
		foreach(\Auth::user()->with('roles')->where('id', \Auth::user()->id)->get()[0]->roles as $role) {

			$permission_roles = Role::with('permissions')->find($role->id);

			foreach ($permission_roles->permissions as $value) {
					if($value->name == $permission) {
						return true;
					}
			}
		}
		if($more_permissions) {
			if(is_null($permission_found)) {
				return ['result' => false, 'permissions' => []];	
			}
			return ['result' => true, 'permissions' => $permission_found];
		}
		return false;
	}


	public static function getPermissionsVue($module){

		$module_id = ModuleGroup::where('name',$module)->value('id');

		foreach(\Auth::user()->with('roles')->where('id', \Auth::user()->id)->get()[0]->roles as $role) {

			$permission_roles = Role::with('permissions')->find($role->id);
			
			return $permission_roles->permissions->where('module_group_id',$module_id)->pluck('name','name')->toarray();
		}
	}
}