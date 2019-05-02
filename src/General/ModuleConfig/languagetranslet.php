
        <?php

        $formelements = [
		"locale" => "",
		
			"value" => [],
			"translation" => [],
			
			// [LanguagetransletArray]
		];
		
        $formelements["_token"] = csrf_token();
		$data =  [
                'lang' => Lang::get('language_translets'),
                'common' => Lang::get('label.common'),
                
                'dir'  => 'languagetranslet',
                'id' => 0,
                'is_visible' => true,
                
                'list_route'  =>  route('languagetranslet.index'),
                'store_route'  => route('languagetranslet.store'),
                'paginate_route'  => route('languagetranslet.paginate'),
                'edit_route'  => route('languagetranslet.edit',''),
                'create_route'  => route('languagetranslet.create'),
                'destory_route' => route('languagetranslet.destroy'),
                'get_activity' => route('get.activity'),
                'get_file' => route('get.file'),
                'get_lang_array_pagination' => route('get.lang_array_pagination'),
				
				// [LanguagetransletModule]
            ];
		$data["locale"] = \Ongoingcloud\Laravelcrud\Helpers::LangCode();
		
		$file = [];
		$filesInFolder = \File::allFiles(base_path('resources/lang/en'));
        foreach($filesInFolder as $path) { 
            $file[] = pathinfo($path);
        } 

		$lang_value = [];

        foreach ($file as $key => $value) {
        	$langs = \Lang::get($value['filename']);
			foreach ($langs as $key => $value) {
				if(!is_array($value)){
					if(!in_array($value, $lang_value)){
						$lang_value[] = $value;	
						$formelements['translation'][$value] = "";				
					}
				} else {
					foreach ($value as $k => $val) {
						if(!is_array($val)) {
							if(!in_array($val, $lang_value)){
								$lang_value[] = $val;
								$formelements['translation'][$val] = "";
							}
						} else {
							foreach ($val as $v) {
								if(!in_array($v, $lang_value)){
									$lang_value[] = $v;
									$formelements['translation'][$v] = "";
								}
							}
						}
					}
				}
			}
        }
        $data['lang_value'] = $lang_value;

		$data["fillable"] = $formelements;
        
		return $data;

        