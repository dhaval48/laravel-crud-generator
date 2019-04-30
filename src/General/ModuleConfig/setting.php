
        <?php

        $formelements = ["enable_registration" => "",
		
		// [SettingArray]
		];
		
        $formelements["_token"] = csrf_token();
		$data =  [
                'lang' => Lang::get('settings'),
                'common' => Lang::get('label.common'),
                
                'dir'  => 'setting',
                'id' => 0,
                'is_visible' => false,
                
                'list_route'  =>  route('setting.index'),
                'store_route'  => route('setting.store'),
                // 'paginate_route'  => route('setting.paginate'),
                // 'edit_route'  => route('setting.edit',''),
                // 'create_route'  => route('setting.create'),
                // 'destory_route' => route('setting.destroy'),
                'get_activity' => route('get.activity'),
                'get_file' => route('get.file'),
                
				// [SettingModule]
            ];
		$data["fillable"] = $formelements;
		
		// [SettingGrid]
		return $data;

        