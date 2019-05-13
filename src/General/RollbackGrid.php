<?php

namespace Ongoingcloud\Laravelcrud\General;

Class RollbackGrid {

	function __construct() {

 	}

	public function deleteFiles($request, $production = false, $delete = false) {
        $project_path_main = env('DEV_PROJECT_PATH');
        if($production) {
            $project_path_main = env('PROD_PROJECT_PATH');
        }

        $controller_name = $this->makeControllerName($request->main_module);

        $this->getAllFiles($request, $project_path_main, $controller_name, $delete);

        $this->replaceModule($request, $project_path_main, $controller_name);

        $this->removeContent($this->field['deletefiles']);
	}

    public function removeContent($files) {

        foreach ($files as  $value) {
            if(file_exists($value)) {
                unlink($value);
            }
        }
    }

    public function makeControllerName($controller) {

        $classArr = explode(' ',$controller);
        if(count($classArr) > 0) {
            $controller = '';
            foreach($classArr as $class) {
                $controller .= strtolower($class);
            }
        }
        return $controller;
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

                $project_path_main."/app/Models/".ucfirst($controller_name).'.php',
                
                // $migration_path,              
            ];

        if($delete) {
            foreach ($migration_path as $key => $value) {
                $this->field['deletefiles'][] = $project_path_main.'/database/migrations/'.$value;
            }
        }

        $this->field['files'] = [
                //Replace key word
                $project_path_main."/app/Http/Controllers/Backend/CommonController.php" => file_get_contents($project_path_main."/app/Http/Controllers/Backend/CommonController.php"),

                $project_path_main."/routes/Common.php" => file_get_contents($project_path_main."/routes/Common.php"),

                $project_path_main.'/resources/js/components/common/grid.vue' => file_get_contents($project_path_main.'/resources/js/components/common/grid.vue'),
            ];
    }

    //Replace MODULE
    public function replaceModule($request, $project_path_main, $controller_name) {
        
        $this->inputFieldsLoop($request, $project_path_main, $controller_name);
               
        // replace word and put content in existing file
        $project_replace_word = [
                                $this->field['dropdown_common_function'] => "",
                                $this->field['common_route'] => "",
                                $this->field['options_data'] => "",
                                $this->field['dropdown_mount'] => "",
                            ];

        foreach ($this->field['files'] as $key => $value) {

            foreach ($project_replace_word as $module_key => $module_value) {
                $value = preg_replace('/'.preg_quote($module_key,'/').'/',$module_value, $value);
                file_put_contents($key, $value);
            }
        }
    }

    // Input Field Array
    public function inputFieldsLoop($request, $project_path_main, $controller_name) {

        $dropdown_common_function = "";
        $common_route = "";
        $options_data = "";
        $dropdown_function = "";
        $dropdown_mount = "";

        for($i=0; $i < count($request->input_name); $i++) {
            if($request->visible[$i]){
                // $name = explode("|", $request->input_name[$i]);
                $input_name = $request->input_name[$i];
                $db_name = $request->db_name[$i];

                if($request->input_type[$i] == 'dropdown'){
                    if($request->key[$i] != "") {

                    $exist_function = \Ongoingcloud\Laravelcrud\Models\Moduleinput::where('db_name', $db_name)
                                            ->whereHas('form_module', function($query) {
                                                $query->whereNotNull('parent_form');
                                            })->get();
                    if(count($exist_function) == 1) {
                        $this->field['deletefiles'][] = $project_path_main."/resources/views/backend/modal/".$request->table[$i].".blade.php";

                        //[OptionsData]
                        $options_data .= $this->optionDataDropdown($db_name);

                        //[DropdownSearch]
                        $dropdown_mount .= $this->dropdownMount($db_name, $request->table[$i]);
                    }

                    $exist_function = \DB::table('module_inputs')->where('db_name', $db_name)->get();
                    if(count($exist_function) == 1) {
                        $this->field['deletefiles'][] = $project_path_main."/resources/views/backend/modal/".$request->table[$i].".blade.php";
                        // common function [Function]
                        $dropdown_common_function .= $this->makeCommon($request, $i, $db_name, $project_path_main);

                        // common route [CommonRoute]
                        $common_route .= $this->commonRoute($request, $i, $db_name); 
                    }

                    } else {
                        //[OptionsData] for empty dropdown
                        $options_data .= $this->optionDataEmptyDropdown($db_name);
                    }
                }
            }
        }
        
        $this->field['options_data'] = $options_data;
        $this->field['dropdown_mount'] = $dropdown_mount;
        $this->field['dropdown_common_function'] = $dropdown_common_function;
        $this->field['common_route'] = $common_route;
    }

    // common fuction [Function]
    public function makeCommon($request, $i, $db_name, $project_path_main) {
            return "\n"."\t"."public function get".ucfirst($db_name)."(".'Request $request'.") {
                \n"."\t"."\t"."return \DB::table('".$request->table[$i]."')->latest()->wherenull('deleted_at')->pluck('".$request->key[$i]."','".$request->value[$i]."');
            \n"."\t"."}";
    }

    // common route [CommonRoute]
    public function commonRoute($request, $i, $db_name) {
        
        $route =  base_path()."/vendor/ongoingcloud/laravelcrud/Vuesample/common.php";
            
        $common = file_get_contents($route);
        
        $common = preg_replace('/\\['.preg_quote('MODULE','/').'\\]/',strtolower($db_name),$common);
        $common = preg_replace('/\\['.preg_quote('UMODULE','/').'\\]/',ucfirst($db_name),$common);

        return $common;
    }

    // [GridOptionsData]
    public function optionDataDropdown($db_name){
        
            return $db_name." : [],"."\n"."\t"."\t"."\t";
    }

    // [OptionsData] for empty dropdown
    public function optionDataEmptyDropdown($db_name){
            return $db_name." : this.module.".$db_name.","."\n"."\t"."\t"."\t";
    }

    // [GridDropdownSearch]
    public function dropdownMount($db_name, $table_name){
        $url = "this.module.".$db_name."_search";

        return "
        if($url) {
            axios.get($url).then(data => {
                this.$db_name = data.data;
            });
        }
        ";
    }
}
