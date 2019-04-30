<?php

$formelements = [
		"parent_module" => "",
		"main_module" => "",
		"is_model" => 0,
		"is_public" => 0,
		"table_name" => "",
		
		"apimodule_id" => [],
			"name" => [],
			"type" => [],
			"validation" => [],
			"default" => [],
			
			// [ApimoduleArray]
		];
		
        $formelements["_token"] = csrf_token();
		$data =  [
                'lang' => Lang::get('api_modules'),
                'common' => Lang::get('label.common'),
                
                'dir'  => 'apimodule',
                'id' => 0,
                'is_visible' => false,
                
                'list_route'  =>  route('apimodule.index'),
                'store_route'  => route('apimodule.store'),
                'paginate_route'  => route('apimodule.paginate'),
                'edit_route'  => route('apimodule.edit',''),
                'create_route'  => route('apimodule.create'),
                'destory_route' => route('apimodule.destroy'),
                'get_activity' => route('get.activity'),
                'get_file' => route('get.file'),
                'parent_form_search' => route('get.parent_api_form'),
				'table_search' => route('get.parent_api_table'),
				'parent_module_search' => route('get.parent_module'),
				
				// [ApimoduleModule]
            ];
		$data["fillable"] = $formelements;
		
		$data["api_tables"] = [
				'Name' => [
					'type' => 'input',
					'name' => 'name',
				],
				'Type' => [
					'type' => 'dropdown',
					'name' => 'type',
					'empty' => true,
				],
				'Validation' => [
					'type' => 'input',
					'name' => 'validation',
				],
				'Default' => [
					'type' => 'input',
					'name' => 'default',
				],
				
			];
			$data["api_tablesrow_count"] = 0;
			$data["api_tables_row"][] = 0;
		$data["type"] = ['varchar','integer','tinyint','date','double'];
			
		// [ApimoduleGrid]
		return $data;