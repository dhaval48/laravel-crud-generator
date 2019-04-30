<?php


$formelements = ["name" => "",
		
		// [PermissionmoduleArray]
		];
		
        $formelements["_token"] = csrf_token();
		$data =  [
                'lang' => Lang::get('permission_modules'),
                'common' => Lang::get('label.common'),
                
                'dir'  => 'permissionmodule',
                'id' => 0,
                'is_visible' => false,
                
                'list_route'  =>  route('permissionmodule.index'),
                'store_route'  => route('permissionmodule.store'),
                'paginate_route'  => route('permissionmodule.paginate'),
                'edit_route'  => route('permissionmodule.edit',''),
                'create_route'  => route('permissionmodule.create'),
                'destory_route' => route('permissionmodule.destroy'),
                'get_activity' => route('get.activity'),
                'get_file' => route('get.file'),
                
				// [PermissionmoduleModule]
            ];
		$data["fillable"] = $formelements;
		
		// [PermissionmoduleGrid]
		return $data;