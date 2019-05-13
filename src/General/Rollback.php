<?php

namespace Ongoingcloud\Laravelcrud\General;

Class Rollback {

    public $sample_component;
    public $sample_route;
    public $sample_side;

    public $field = [];

    function __construct() {
        $this->sample_component = base_path()."/vendor/ongoingcloud/laravelcrud/Vuesample/component.js";
        $this->sample_route = base_path()."/vendor/ongoingcloud/laravelcrud/Vuesample/Route.php";
        $this->sample_side = base_path()."/vendor/ongoingcloud/laravelcrud/Vuesample/side.php";
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

        if($delete) {
            $module_group = \DB::table('module_groups')->where('name', $controller_name)->first();
            if($module_group) {
                \DB::table('module_groups')->where('name', $controller_name)->delete();
            }
        }

        if(empty($request->parent_module)) { 
            $this->sample_route = base_path()."/vendor/ongoingcloud/laravelcrud/Vuesample/WithoutPrefixRoute.php";
        }

        // if(file_exists($project_path_main."/app/Http/Controllers/Backend/".ucfirst($controller_name).'Controller.php')) {
        $this->getAllFiles($request, $project_path_main, $controller_name, $delete);

        $this->getSampleContent();
        
        $this->replaceModule($request, $project_path_main, $controller_name);

        $this->removeContent($this->field['deletefiles']);
        $this->removeFolder($this->field['folder']);
        // }
    }

    public function removeContent($files) {

        foreach ($files as  $value) {
            if(file_exists($value)) {
                unlink($value);
            }
        }
    }

    public function removeFolder($dirs) {
        foreach ($dirs as  $dir) {
            if(is_dir($dir)) {
                foreach(scandir($dir) as $file) {
                    if ('.' === $file || '..' === $file) continue;
                    if (is_dir("$dir/$file")) rmdir_recursive("$dir/$file");
                    else unlink("$dir/$file");
                }
                rmdir($dir);
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
        
        $vueFolder = $project_path_main.'/resources/js/components/backend/'.strtolower($controller_name);

        $migration_path = $this->getMigrationPath($request, $project_path_main);   

        $this->field['deletefiles'] = [

                //Whole file change
                $project_path_main."/app/Http/Controllers/Backend/".ucfirst($controller_name).'Controller.php',

                $project_path_main."/app/Models/".ucfirst($controller_name).'.php',

                $project_path_main."/resources/views/backend/modules/".strtolower($controller_name).'-table.blade.php',

                $project_path_main."/resources/views/backend/modules/".strtolower($controller_name).'.blade.php',
                
                // $migration_path,

                // $project_path_main."/app/General/ModuleConfig/".strtolower($controller_name).'.php',

                $project_path_main."/resources/lang/en/".strtolower($request->table_name).'.php',

                $project_path_main."/tests/Browser/Backend/".ucfirst($controller_name).'Test.php',
            ];

        if($delete) {
            foreach ($migration_path as $key => $value) {
                $this->field['deletefiles'][] = $project_path_main.'/database/migrations/'.$value;
            }
            $this->field['deletefiles'][] = $project_path_main."/app/General/ModuleConfig/".strtolower($controller_name).'.php';
        }

        $this->field['folder'] = [
                $vueFolder,

                $project_path_main."/app/Http/Requests".'/'.ucfirst($controller_name),
            ];
               
        $this->field['files'] = [
                //Replace key word
                $project_path_main.'/routes/web.php' => file_get_contents($project_path_main.'/routes/web.php'),

                $project_path_main."/app/Http/Controllers/Backend/CommonController.php" => file_get_contents($project_path_main."/app/Http/Controllers/Backend/CommonController.php"),

                $project_path_main."/routes/Common.php" => file_get_contents($project_path_main."/routes/Common.php"),

                $project_path_main."/resources/js/component.js" => file_get_contents($project_path_main."/resources/js/component.js"),

                // $project_path_main."/resources/views/backend/includes/side.blade.php" => file_get_contents($project_path_main."/resources/views/backend/includes/side.blade.php"),

                $project_path_main."/app/General/ModuleConfig.php" => file_get_contents($project_path_main."/app/General/ModuleConfig.php"),
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
        
        $this->inputFieldsLoop($request, $project_path_main, $controller_name);

        $replace_word = [
                        'TNAME' => strtolower($request->table_name),
                        'LNAME' => strtolower($controller_name),
                        'UMODULE' => ucfirst($controller_name),
                        'MODULE' => strtolower($controller_name),
                        'ULABEL' => ucwords($request->main_module),
                        'LAMODULE' => trim(strtolower(str_replace(' ','-',$request->parent_module))),
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
                                $this->sample_route => "",
                                $this->field['dropdown_common_function'] => "",
                                $this->field['common_route'] => "",
                                $this->sample_component => "",
                                // $this->sample_side => "",
                                $this->ModuleConfig($request, $controller_name) => "",
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

        $lang = "";
        $dropdown_common_function = "";
        $common_route = "";


        for($i=0; $i < count($request->input_name); $i++) {
            if($request->visible[$i]){
                // $name = explode("|", $request->input_name[$i]);
                $input_name = $request->input_name[$i];
                $db_name = $request->db_name[$i];

                if($request->input_type[$i] == 'dropdown'){
                    $exist_function = \DB::table('module_inputs')->where('db_name', $db_name)->get();
                    if(count($exist_function) == 1) {
                        $this->field['deletefiles'][] = $project_path_main."/resources/views/backend/modal/".$request->table[$i].".blade.php";

                        // common function [Function]
                        $dropdown_common_function .= $this->makeCommon($request, $i, $db_name, $project_path_main);

                        // common route [CommonRoute]
                        $common_route .= $this->commonRoute($request, $i, $db_name); 
                    }
                }
            }
        }
        
        $this->field['dropdown_common_function'] = $dropdown_common_function;
        $this->field['common_route'] = $common_route;
    }

    // array content [Moduleconfig]
    private function ModuleConfig($request, $controller_name) {
        return "
    public static function ".strtolower($request->table_name)."() {
        return include 'ModuleConfig/".strtolower($controller_name).".php';
    }";

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
}
