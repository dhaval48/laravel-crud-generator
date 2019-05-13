<?php

namespace Ongoingcloud\Laravelcrud\General;

Class ApiRollback {

	public $api_route;

	public $field = [];

	function __construct() {
        $this->api_route = base_path()."/vendor/ongoingcloud/laravelcrud/Apisample/Route.php";
	}

	public function getSampleContent() {
		foreach ($this as $key => $value) {

			if($key != 'field') {
				if(!empty($value)) {

                    $this->$key = file_get_contents($value);
                }
			}
		} 
	}

	public function deleteFiles($request, $production = false, $delete = false) {
        $project_path_main = env('DEV_PROJECT_PATH');
        if($production) {
            $project_path_main = env('PROD_PROJECT_PATH');
        }

        $controller_name = $this->makeControllerName($request);

        $this->getAllFiles($request, $project_path_main, $controller_name, $delete);

        $this->removeContent($this->field['deletefiles']);

        if(isset($request->is_public) && $request->is_public){
            $this->api_route = base_path()."/vendor/ongoingcloud/laravelcrud/Apisample/PublicRoute.php";
        }

        $this->getSampleContent();
        
        $this->replaceModule($request, $project_path_main, $controller_name);
	}

    public function removeContent($files) {

        foreach ($files as  $value) {
            if(file_exists($value)) {
                unlink($value);
            }
        }
    }

    public function getMigrationPath($request, $project_path_main) {
        $file_name = $request->table_name."_table";

        $migration_path = $project_path_main."/database/migrations";
        
        return $this->existDirFile($migration_path, $file_name); 
    }

    // scan dir matching file 
    public function existDirFile($path, $file_name) {
        $existing_path = [];
        $files = scandir($path);

        foreach ($files as $value) {
            if(strrpos($value, $file_name)) {
                $existing_path[] = $value;
            }
        }
        return $existing_path;
    }

    public function getAllFiles($request, $project_path_main, $controller_name, $delete) {
        
        $migration_path = $this->getMigrationPath($request, $project_path_main);        

        $this->field['deletefiles'] = [

                //Whole file change
                $project_path_main."/app/Http/Controllers/API/".ucfirst($controller_name).'Controller.php',
                $project_path_main."/tests/Unit/Api/".ucfirst($controller_name).'.php',               
            ];

        if(isset($request->is_model) && $request->is_model){
            if($delete) {
                foreach ($migration_path as $key => $value) {
                    $this->field['deletefiles'][] = $project_path_main.'/database/migrations/'.$value;
                }
                $this->field['deletefiles'][] = $project_path_main."/app/Models/".ucfirst($controller_name).'.php';
            }                
        }
               
        $this->field['files'] = [
                //Replace key word
                $project_path_main.'/routes/api.php' => file_get_contents($project_path_main.'/routes/api.php'),
            ];

    }

    public function makeControllerName($request) {
        $controller_name = ucfirst($request->main_module);

        $classArr = explode(' ',$controller_name);
        if(count($classArr) > 0) {
            $controller_name = '';
            foreach($classArr as $class) {
                $controller_name .= strtolower($class);
            }
        }
        return $controller_name;
    }

	//Replace MODULE
	public function replaceModule($request, $project_path_main, $controller_name) {

		$replace_word = [
                        'UMODULE' => ucfirst($controller_name),
                        'MODULE' => strtolower($controller_name),
					];

		
		foreach ($this as $key => $value) {
			if($key != 'field') {
				foreach ($replace_word as $module_key => $module_value) {
					$this->$key = preg_replace('/\\['.preg_quote($module_key,'/').'\\]/',$module_value,$this->$key);
                }
			}
		}                
        // replace word and put content in existing file
        $project_replace_word = [
                                $this->api_route => "",
                            ];
        
        foreach ($this->field['files'] as $key => $value) {

            foreach ($project_replace_word as $module_key => $module_value) {

                $value = preg_replace('/'.preg_quote($module_key,'/').'/',$module_value, $value);
                file_put_contents($key, $value);
            }
        }
	}
}
