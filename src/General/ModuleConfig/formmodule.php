<?php


$formelements = [
		"parent_module" => "",
		"main_module" => "",
		"table_name" => "",
		
		"name" => [],
		"type" => [],
		"validation" => [],
		"default" => ['null'],
		"formmodule_id" => [],
		
		"visible" => [true],
		"input_name" => [],
		"db_name" => [],
		"input_type" => [],
		"key" => [],
		"value" => [],
		"table" => [],
			
			// [FormmoduleArray]
		];
		
        $formelements["_token"] = csrf_token();
		$data =  [
                'lang' => Lang::get('form_modules'),
                'common' => Lang::get('label.common'),
                
                'dir'  => 'formmodule',
                'id' => 0,
                'is_visible' => false,
                
                'list_route'  =>  route('formmodule.index'),
                'store_route'  => route('formmodule.store'),
                'paginate_route'  => route('formmodule.paginate'),
                'edit_route'  => route('formmodule.edit',''),
                'create_route'  => route('formmodule.create'),
                'destory_route' => route('formmodule.destroy'),
                'get_activity' => route('get.activity'),
                'get_file' => route('get.file'),
                'parent_form_search' => route('get.parent_form'),
				'table_search' => route('get.table'),
				'table_data_search' => route('get.table_data'),
				'parent_module_search' => route('get.parent_module'),
				
				
				
				// [FormmoduleModule]
            ];
		$data["fillable"] = $formelements;
		
		$data["module_tables"] = [
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
		$data["module_tablesrow_count"] = 0;
		$data["module_tables_row"][] = 0;
		$data["type"] = ['varchar','integer','tinyint','date','double'];
			
		$data["module_inputs"] = [
				'Name' => [
					'type' => 'input',
					'name' => 'input_name',
				],
				'Type' => [
					'type' => 'dropdown',
					'name' => 'input_type',
					'empty' => true,
				],
				'Table' => [
					'type' => 'dropdown',
					'name' => 'table',
				],
				'Value' => [
					'type' => 'dropdown',
					'name' => 'value',
				],
				'Label' => [
					'type' => 'dropdown',
					'name' => 'key',
				],
			];
		$data['disabled'] = ['true'];
		$data["module_inputsrow_count"] = 0;
		$data["module_inputs_row"][] = 0;
		$data["input_type"] = ['input','dropdown','file','checkbox','textarea','date','radio'];
			
		// [FormmoduleGrid]
		return $data;