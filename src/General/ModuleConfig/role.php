<?php


$formelements = [
                        "name" => "",
                        "description" => "",
                        "module_id" => [],
                        "module_group_id" => [],
                        "permission_id" => [],
                    ];
		
        $formelements["_token"] = csrf_token();
			$data =  [
                'lang' => Lang::get('roles'),
                'common' => Lang::get('label.common'),
                
                'dir'  => 'role',
                'id' => 0,
                'is_visible' => true,
                  // 'title' => Lang::get('role.create_title'),
                'list_route'  => route('role.index'),
                'store_route'  => route('role.store'),
                'paginate_route'  => route('role.paginate'),
                'edit_route'  => route('role.edit',''),
                'create_route'  => route('role.create'),
                'destory_route' => route('role.destroy'),
                'get_activity' => route('get.activity'),
                
            ];
			$data["fillable"] = $formelements;
			return $data;