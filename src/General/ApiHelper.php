<?php

namespace Ongoingcloud\Laravelcrud\General;

use Ongoingcloud\Laravelcrud\Helpers;

Class ApiHelper {

    public $api_controller;
    public $api_test_case;
    public $api_migration;
    public $api_model;
    public $api_route;

    public $field = [];

    function __construct() {
        $this->api_controller = base_path()."/vendor/ongoingcloud/laravelcrud/Apisample/Controller.php";
        $this->api_test_case = base_path()."/vendor/ongoingcloud/laravelcrud/Apisample/apiTestCase.php";
        $this->api_model = base_path()."/vendor/ongoingcloud/laravelcrud/Apisample/Model.php";
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

    public function makeFiles($request, $production = false,  $old_data = "") {
        $project_path_main = env('DEV_PROJECT_PATH');
        if($production) {
            $project_path_main = env('PROD_PROJECT_PATH');
        }

        if(isset($request->is_public) && $request->is_public){
            $this->api_route = base_path()."/vendor/ongoingcloud/laravelcrud/Apisample/PublicRoute.php";
        }

        $table_fields = $this->getTableFields($request, $old_data);
        if(!empty($this->field['table_fields'])) {
            $this->api_migration = base_path()."/vendor/ongoingcloud/laravelcrud/Vuesample/UpdateMigration.php";
        } else {
            $this->api_migration = base_path()."/vendor/ongoingcloud/laravelcrud/Apisample/Migration.php";
        }

        $this->getSampleContent();
        
        $this->replaceModule($request, $project_path_main, $old_data);
        
    }

    public function makeClassName($request) {
        $class_name = ucfirst($request->table_name);

        $classArr = explode('_',$class_name);
        if(count($classArr) > 0) {
            $class_name = '';
            foreach($classArr as $class) {
                $class_name .= ucfirst($class);
            }
        }
        return $class_name;
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
    public function replaceModule($request, $project_path_main, $old_data) {
        $controller_name = $this->makeControllerName($request);

        $this->tableFieldsLoop($request);

        $table_fields = $this->getTableFields($request, $old_data);
        if(!empty($this->field['table_fields'])) {
            $table_fields = $this->field['table_fields'];
        }

        $class_name = $this->makeClassName($request);

        $random = rand();
        $this->field['migration_file_name'] = $random."_".$request->table_name;

        $replace_word = [
                        'MODULE' => strtolower($controller_name),
                        'UMODULE' => ucfirst($controller_name),
                        'ULABEL' => ucwords($request->main_module),
                        'UNAME' => ucfirst($controller_name),
                        'VALIDATION' => $this->field['validation'],
                        'TESTCASEDATA' => $this->field['test_case_data'],
                        'TABLE_NAME' => strtolower($request->table_name),

                        'CLASS_MODULE' => $class_name,
                        'TABLE_FIELDS' => $table_fields,
                        'CLASS_UPDATE_MODULE' => $random."".$class_name,

                        'FILLEBLE_LINES' => $this->field['fillable_lines'],
                        'SEARCHELEMENT_LINES' => $this->field['searchelement'],

                        'GridSave' => "// [GridSave]",
                        'GridEdit' => "// [GridEdit]",
                        'GridValidation' => "// [GridValidation]",
                        'GridDelete' => "// [GridDelete]",
                        'ModelArray' => "// [ModelArray]",
                        'Relation' => "// [Relation]",
                    ];

        foreach ($this as $key => $value) {
            if($key != 'field') {
                $this->$key = str_replace("//","",$this->$key);
                $this->$key = str_replace("{{-- ","",$this->$key);
                $this->$key = str_replace(" --}}","",$this->$key);

                foreach ($replace_word as $module_key => $module_value) {
                    
                    $this->$key = preg_replace('/\\['.preg_quote($module_key,'/').'\\]/',$module_value,$this->$key);                    
                }
            }
        }        

        $this->getAllFiles($request, $project_path_main, $controller_name, $old_data);
        
        $this->putContentDhaval($request, $project_path_main, $this->field['deletefiles']);

        // replace word and put content in existing file
        $project_replace_word = [
                                "// [RouteArray]" => $this->api_route."\n"."// [RouteArray]",
                            ];
        
        foreach ($this->field['files'] as $key => $value) {

            foreach ($project_replace_word as $module_key => $module_value) {

                $value = preg_replace('/'.preg_quote($module_key,'/').'/',$module_value, $value);
                $value = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n"."\n", $value);
                file_put_contents($key, $value);
            }
        }
    }

    public function putContentDhaval($request, $project_path_main, $files) {

        foreach ($files as $key => $value) {
            if(file_exists($key)) {
                unlink($key);
            }
            file_put_contents($key, $value);
        }
    }

    public function getMigrationPath($request, $project_path_main, $old_data) {

        $file_name = $request->table_name."_table";

        $migration_path = $project_path_main."/database/migrations";
        
        // $existing_path = $this->existDirFile($migration_path, $file_name); 

        // if($existing_path != '') {
        //     $migration_path = $project_path_main.'/database/migrations/'.$existing_path;
        
        if(!empty($this->field['table_fields']) && !empty($old_data)) {
            $path = exec("php ".$project_path_main."/artisan make:migration add_column_".$this->field['migration_file_name']."_table");
            $migration_file = str_replace('Created Migration: ', '',$path);
            $migration_file = $migration_file.'.php';
            $migration_path = $project_path_main.'/database/migrations/'.$migration_file;
        } else {
            if($old_data == "" && $request->is_model) {
                $path = exec("php ".$project_path_main."/artisan make:migration create_".$request->table_name."_table");
                $migration_file = str_replace('Created Migration: ', '',$path);
                $migration_file = $migration_file.'.php';
                $migration_path = $project_path_main.'/database/migrations/'.$migration_file;
            }
        }

        return $migration_path;
    }

    // scan dir matching file 
    public function existDirFile($path, $file_name) {
        $existing_path = '';
        $files = scandir($path);

        foreach ($files as $value) {
            if(strrpos($value, $file_name)) {
                $existing_path = $value;
            }
        }
        return $existing_path;
    }


    public function getAllFiles($request, $project_path_main, $controller_name, $old_data) {

        $migration_path = $this->getMigrationPath($request, $project_path_main, $old_data);        

        $this->field['deletefiles'] = [

                //Whole file change
                $project_path_main."/app/Http/Controllers/API/".ucfirst($controller_name).'Controller.php' => $this->api_controller,

                $project_path_main."/tests/Unit/Api/".ucfirst($controller_name).'.php' => $this->api_test_case,
            ];

        if(isset($request->is_model) && $request->is_model){
            $this->field['deletefiles'] [$project_path_main."/app/Models/".ucfirst($controller_name).'.php'] = $this->api_model;

            if(!empty($this->field['table_fields'])) {
                $this->field['deletefiles'] [$migration_path] = $this->api_migration;
            } else {
                if($old_data == "") {
                    $this->field['deletefiles'] [$migration_path] = $this->api_migration;
                }
            }
        }
               
        $this->field['files'] = [
                //Replace key word
                $project_path_main.'/routes/api.php' => file_get_contents($project_path_main.'/routes/api.php'),
            ];

    }

    // Table Field Array
    public function tableFieldsLoop($request) {

        $fillable_lines = "";
        $searchelement = "";
        $test_data = "";
        
        //[VALIDATION]
        $this->getValidation($request);
        
        for($i=0; $i < count($request->name); $i++) {
    
            // [FILLEBLE_LINES]
            $fillable_lines .= $this->prepareFillable($request->name[$i]);

            // [SEARCHELEMENT_LINES]
            $searchelement .= $this->prepareFillable($request->name[$i]);
            
            // [TESTCASEDATA]
            $test_data .= $this->testData($request->name[$i], $request->type[$i]);
        }
        $this->field['test_case_data'] = $test_data;
        $this->field['fillable_lines'] = $fillable_lines;
        $this->field['searchelement'] = $searchelement;
    }

    //[VALIDATION]
    public function getValidation($request) {
        
        $validation_rule = '';
        
        foreach ($request->validation as $key => $value) {
            if($value != '') {
        
                $validation_rule .= '"'.$request->name[$key].'" => "required",'."\n"."\t"."\t"."\t";
        
            }
        
        }
        $this->field['validation'] = $validation_rule;
    }

    // TABLE_FIELDS
    public function getTableFields($request, $old_data) {
        $this->field['table_fields'] = "";
        $new_line = "\n"."\t"."\t"."\t";

        if($old_data) {
            $limit = count($request->name);
            $old_limit = count($old_data->name);

            $diff_data = array_intersect($old_data->module_input_id, $request->module_input_id);

            for($i=0; $i<$old_limit; $i++) {
                if(!isset($diff_data[$i])) {
                    if(!strrpos($this->field['table_fields'], 'dropColumn("'.$old_data->name[$i].'")')) {
                        $this->field['table_fields'] .= '$table->dropColumn("'.$old_data->name[$i].'");'.$new_line;
                    }
                }
            }

            for($j=0; $j<$limit; $j++) {

                $n_name = $request->name[$j]; 
                $n_type = $request->type[$j];

                if(isset($request->module_input_id[$j])) {

                    $module_table_data = \DB::table('api_tables')->where('id', $request->module_input_id[$j])->first();

                    if($module_table_data->type != $request->type[$j]) {
                        if($request->type[$j] == 'varchar') {
                            $fields = '$table->string("'.$module_table_data->name.'")->change();'.$new_line;
                        } else if($request->type[$j] == 'integer' && $request->name!= 'id') {
                            $fields = '$table->integer("'.$module_table_data->name.'")->change();'.$new_line;
                        } else if($request->type[$j] == 'tinyint') {
                            $fields = '$table->tinyinteger("'.$module_table_data->name.'")->change();'.$new_line;
                        } else if($request->type[$j] == 'double') {
                            $fields = '$table->double("'.$module_table_data->name.'",10,2)->change();'.$new_line;
                        } else if($request->type[$j] == 'date') {
                            $fields = '$table->date("'.$module_table_data->name.'")->change();'.$new_line;
                        }

                        // if($request->default[$j] == 'null') {
                        //     $fields .= '->nullable()->change();'.$new_line;
                        // } elseif($request->default[$j] != '') {
                        //     $fields .= '->default("'.$request->default[$j].'")->change();'.$new_line;
                        // } else {
                        //     $fields .= '->nullable()->change();'.$new_line;
                        // }
                        $this->field['table_fields'] .= $fields;
                    }

                    if($module_table_data->default != $request->default[$j]) {
                        if($request->type[$j] == 'varchar') {
                            $fields = '$table->string("'.$module_table_data->name.'")';
                        } else if($request->type[$j] == 'integer' && $request->name!= 'id') {
                            $fields = '$table->integer("'.$module_table_data->name.'")';
                        } else if($request->type[$j] == 'tinyint') {
                            $fields = '$table->tinyinteger("'.$module_table_data->name.'")';
                        } else if($request->type[$j] == 'double') {
                            $fields = '$table->double("'.$module_table_data->name.'",10,2)';
                        } else if($request->type[$j] == 'date') {
                            $fields = '$table->date("'.$module_table_data->name.'")';
                        }

                        if($request->default[$j] == 'null') {
                            $fields .= '->nullable()->change();'.$new_line;
                        } elseif($request->default[$j] != '') {
                            $fields .= '->default("'.$request->default[$j].'")->change();'.$new_line;
                        } else {
                            $fields .= '->nullable()->change();'.$new_line;
                        }
                        $this->field['table_fields'] .= $fields;
                    }

                    if($module_table_data->name != $request->name[$j]) {                        
                        $fields = '$table->renameColumn("'.$module_table_data->name.'", "'.$request->name[$j].'");'.$new_line;
                        $this->field['table_fields'] .= $fields;
                    }
                    
                } else {
                    if(!isset($request->module_input_id[$j])) {

                        if($n_type == 'varchar') {
                            $fields = '$table->string("'.$n_name.'")';
                        } else if($n_type == 'integer' && $n_name!= 'id') {
                            $fields = '$table->integer("'.$n_name.'")';
                        } else if($n_type == 'tinyint') {
                            $fields = '$table->tinyinteger("'.$n_name.'")';
                        } else if($n_type == 'double') {
                            $fields = '$table->double("'.$n_name.'",10,2)';
                        } else if($n_type == 'date') {
                            $fields = '$table->date("'.$n_name.'")';
                        }

                        if($request->default[$j] == 'null') {
                            $fields .= '->nullable();'.$new_line;
                        } elseif($request->default[$j] != '') {
                            $fields .= '->default("'.$request->default[$j].'");'.$new_line;
                        } else {
                            $fields .= '->nullable();'.$new_line;
                        }
                        $this->field['table_fields'] .= $fields;
                    }
                }
            }
        } else {

            $table_fields = '$table->increments("id");'.$new_line;
            foreach($request->name as $key=>$name) {
                $fields = '';
                if($request->type[$key] == 'varchar') {
                    $fields = '$table->string("'.$name.'")';
                } else if($request->type[$key] == 'integer' && $name!= 'id') {
                    $fields = '$table->integer("'.$name.'")';
                } else if($request->type[$key] == 'tinyint') {
                    $fields = '$table->tinyinteger("'.$name.'")';
                } else if($request->type[$key] == 'double') {
                    $fields = '$table->double("'.$name.'",10,2)';
                } else if($request->type[$key] == 'date') {
                    $fields = '$table->date("'.$name.'")';
                }

                if($request->default[$key] == 'null') {
                    $fields .= '->nullable();'.$new_line;
                } elseif($request->default[$key] != '') {
                    $fields .= '->default("'.$request->default[$key].'");'.$new_line;
                } else {
                    $fields .= '->nullable();'.$new_line;
                }
                
                $table_fields .= $fields;
            }
            $table_fields .= '$table->timestamps();'.$new_line.'$table->softDeletes();'.$new_line.$new_line;

            return $table_fields;
        }
    }

    // [FILLEBLE_LINES]
    public function prepareFillable($value) {

        return "'".strtolower($value)."',"."\n"."\t"."\t"."\t"."\t";
    }

    // [TESTCASEDATA]
    public function testData($value, $type) {

        if($type == 'varchar') {
            return "'".strtolower($value)."'"." => "."'Test-Case',"."\n"."\t"."\t"."\t"."\t"."\t"."\t";
        } else if($type == 'integer') {
            return "'".strtolower($value)."'"." => 1,"."\n"."\t"."\t"."\t"."\t"."\t"."\t";
        } else if($type == 'tinyint') {
            return "'".strtolower($value)."'"." => 1,"."\n"."\t"."\t"."\t"."\t"."\t"."\t";
        } else if($type == 'double') {
            return "'".strtolower($value)."'"." => 10.04,"."\n"."\t"."\t"."\t"."\t"."\t"."\t";
        } else if($type == 'date') {
            return "'".strtolower($value)."'"." => "."'".Helpers::parseDBdate(date('m/d/Y'))."',"."\n"."\t"."\t"."\t"."\t"."\t"."\t";
        }
    }
}
