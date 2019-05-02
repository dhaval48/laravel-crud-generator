<?php

namespace Ongoingcloud\Laravelcrud\General;

use Ongoingcloud\Laravelcrud\Helpers;

Class Grid {

	public $parent_controller;
	public $parent_html;
	public $common_grid_vue;
	public $parent_model;
    public $sample_modal;
    public $parent_blade;
	public $parent_relation;

	public $field = [];

	function __construct() {

        $this->sample_modal = base_path()."/vendor/ongoingcloud/laravelcrud/Vuesample/modal.php";
        
        $this->parent_relation = base_path()."/vendor/ongoingcloud/laravelcrud/Vuesample/parentrelation.php";
        $this->sample_gridmodel = base_path()."/vendor/ongoingcloud/laravelcrud/Vuesample/GridModel.php";
 	}

    public function loadFilePath($request, $project_path_main, $parent_controller_name) {

        $this->parent_controller = $project_path_main."/app/Http/Controllers/Backend/".ucfirst($parent_controller_name)."Controller.php";

        $this->parent_model = $project_path_main."/app/Models/".ucfirst($parent_controller_name).".php";

        $this->parent_html = $project_path_main.'/resources/js/components/backend/'.strtolower($parent_controller_name)."/html.vue";

        $this->parent_blade =  $project_path_main.'/resources/views/backend/modules/'.strtolower($parent_controller_name).'.blade.php';

        $this->common_grid_vue = $project_path_main.'/resources/js/components/common/grid.vue';
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

	public function makeFiles($request, $production = false, $old_data = "") {

        $project_path_main = env('DEV_PROJECT_PATH');
        if($production) {
            $project_path_main = env('PROD_PROJECT_PATH');
        }

        $parent_controller_name = $this->makeControllerName($request->parent_form);

        $parent_table_name = \DB::table('form_modules')->where('main_module', $request->parent_form)->wherenull('deleted_at')->wherenull('parent_form')->first()->table_name;

        $controller_name = $this->makeControllerName($request->main_module);

        $this->getTableFields($request, $parent_controller_name, $parent_table_name, $old_data);
        if(!empty($this->field['table_fields'])) {
            $this->sample_gridmigration = base_path()."/vendor/ongoingcloud/laravelcrud/Vuesample/UpdateMigration.php";
        } else {
            $this->sample_gridmigration = base_path()."/vendor/ongoingcloud/laravelcrud/Vuesample/GridMigration.php";
        }

        $this->loadFilePath($request, $project_path_main, $parent_controller_name);

		$this->getSampleContent();

    	$this->replaceModule($request, $project_path_main, $parent_controller_name, $controller_name, $parent_table_name, $old_data);
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

	//Replace MODULE
	public function replaceModule($request, $project_path_main, $parent_controller_name, $controller_name, $parent_table_name, $old_data) {

        $this->tableFieldsLoop($request);

        $this->inputFieldsLoop($request, $project_path_main, $parent_controller_name);

        $table_fields = $this->getTableFields($request, $parent_controller_name, $parent_table_name, $old_data);
        if(!empty($this->field['table_fields'])) {
            $table_fields = $this->field['table_fields'];
        }

        $class_name = $this->makeClassName($request);

        $random = rand();
        $this->field['migration_file_name'] = $random."_".$request->table_name;

        $replace_word = [
                        '// [GridOptionsData]'   => $this->field['options_data'],
                        '// [GridDropdownSearch]' => $this->field['dropdown_mount'],
                        '// [Module_Data]' => $this->field['module_data'],
                        '// [GridSave]' => $this->gridSave($request, $parent_controller_name),
                        '// [GridValidation]' => $this->gridValidation($request),
                        '// [GridActivity]' => $this->gridActivityArray($request, $parent_controller_name),
                        '// [GridDelete]' => $this->gridDelete($request, $parent_controller_name),
                        '// [GridEdit]' => $this->gridEdit($request),
                        '// [GRID_RESET]' => $this->gridReset($request),
                        '{{-- [Modal_path] --}}' => $this->field['modal_path'],
                        '<!-- [GridVueElement-1] -->' => $this->field['grid_html'],
                        '// [ModelArray]' => $this->field['formelement']."\n"."\t"."\t"."\t"."// [ModelArray]",
                        '// [Relation]' => "\n".$this->parent_relation."\n"."\t".'// [Relation]',
                    ];

        $replace_word_existing = [
                                'UMODULE' => ucfirst($controller_name),
                                'TNAME' => strtolower($request->table_name),
                                'UNAME' => ucfirst($controller_name),
                                'TABLE_NAME' => strtolower($request->table_name),
                                'FILLEBLE_LINES' => $this->field['fillable_lines'],
                                'PMODULE' => strtolower($parent_controller_name),
                                'CLASS_MODULE' => $class_name,
                                'TABLE_FIELDS' => $table_fields,
                                'CLASS_UPDATE_MODULE' => $random."".$class_name,
                                
                             ];
        

        foreach ($this as $key => $value) {
            
            if($key != 'field') {

                foreach ($replace_word as $module_key => $module_value) {
                    $this->$key = preg_replace('/'.preg_quote($module_key,'/').'/',$module_value,$this->$key);
                }

                foreach ($replace_word_existing as $existing_key => $existing_value) {
                    $this->$key = preg_replace('/\\['.preg_quote($existing_key,'/').'\\]/',$existing_value,$this->$key);
                }
            }
        }
        $this->getAllFiles($request, $project_path_main, $parent_controller_name, $controller_name, $old_data);
        
        $this->putContentDhaval($request, $project_path_main, $this->field['existfiles']);

        // replace word and put content in existing file
        $project_replace_word = [
                                "// [Function]" => $this->field['dropdown_common_function']."\n"."\t".'// [Function]',
                                "// [CommonRoute]" => $this->field['common_route']."\n"."// [CommonRoute]",
                                "// [".ucfirst($parent_controller_name)."Module]" => $this->field['dropdown_search_url'],
                                '// ['.ucfirst($parent_controller_name).'Grid]' => $this->field['grid_element'],
                                '// ['.ucfirst($parent_controller_name).'Array]' => $this->field['formelement']."\n"."\t"."\t"."\t"."// [".ucfirst($parent_controller_name)."Array]",
                            ];

        foreach ($this->field['files'] as $key => $value) {

            foreach ($project_replace_word as $module_key => $module_value) {

                $value = preg_replace('/'.preg_quote($module_key,'/').'/',$module_value, $value);
                file_put_contents($key, $value);
            }
        }
	}


    public function gridReset($request){
        return '
            if(this.module.id == 0) {

                var grid = this.module.'.$request->table_name.';
                
                for(var key in grid) {                        
                   this.$refs.'.$request->table_name.'.form[grid[key].name] = [""];
                }


                this.$refs.'.$request->table_name.'.rows = [];
                this.$refs.'.$request->table_name.'.rows = [0];
                this.$refs.'.$request->table_name.'.index = 0;

                
                this.$parent._data.form = new Form(this.module.fillable);
                this.form = this.$parent._data.form;
                this.$refs.'.$request->table_name.'.form  = this.module.fillable;

            }

            // [GRID_RESET]
        ';
    }

    public function putContentDhaval($request, $project_path_main, $files) {

        foreach ($files as $key => $value) {
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
            if($old_data == "") {
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


    // TABLE_FIELDS
    public function getTableFields($request, $parent_controller_name, $parent_table_name, $old_data) {
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

                    $module_table_data = \DB::table('module_tables')->where('id', $request->module_input_id[$j])->first();

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

                if($name == $parent_controller_name.'_id') {
                    $fields .= '->unsigned();'.$new_line;
                } else {
                    if($request->default[$key] == 'null') {
                    $fields .= '->nullable();'.$new_line;
                    } elseif($request->default[$key] != '') {
                        $fields .= '->default("'.$request->default[$key].'");'.$new_line;
                    }  else {
                        $fields .= '->nullable();'.$new_line;
                    }
                }
                
                $table_fields .= $fields;
            }

            $table_fields .= '$table->timestamps();'.$new_line.$new_line;

            $table_fields .= '$table->foreign("'.$parent_controller_name.'_id")->references("id")->on("'.$parent_table_name.'")
                                ->onUpdate("restrict")
                                ->onDelete("cascade");';

            return $table_fields;
        }
    }

    public function getAllFiles($request, $project_path_main, $parent_controller_name, $controller_name, $old_data) {

        $migration_path = $this->getMigrationPath($request, $project_path_main, $old_data);  
        
        $this->field['existfiles'] = [
                $project_path_main."/app/Http/Controllers/Backend/".ucfirst($parent_controller_name)."Controller.php" => $this->parent_controller,

                $project_path_main."/app/Models/".ucfirst($parent_controller_name).".php" => $this->parent_model,

                $project_path_main.'/resources/js/components/backend/'.strtolower($parent_controller_name)."/html.vue" => $this->parent_html,

                $project_path_main.'/resources/views/backend/modules/'.strtolower($parent_controller_name).'.blade.php' => $this->parent_blade,

                $project_path_main."/app/Models/".ucfirst($controller_name).'.php' => $this->sample_gridmodel,

                // $migration_path => $this->sample_gridmigration,
                
                $project_path_main.'/resources/js/components/common/grid.vue' => $this->common_grid_vue,

            ];

        if(!empty($this->field['table_fields'])) {
            $this->field['existfiles'][$migration_path] = $this->sample_gridmigration;
        } else {
            if($old_data == "") {
                $this->field['existfiles'][$migration_path] = $this->sample_gridmigration;
            }
        }

        $this->field['files'] = [
                $project_path_main."/app/Http/Controllers/Backend/CommonController.php" => file_get_contents($project_path_main."/app/Http/Controllers/Backend/CommonController.php"),

                $project_path_main."/routes/Common.php" => file_get_contents($project_path_main."/routes/Common.php"),

                $project_path_main."/app/General/ModuleConfig/".strtolower($parent_controller_name).".php" => file_get_contents($project_path_main."/app/General/ModuleConfig/".strtolower($parent_controller_name).".php"),
            ];

        if(isset($this->field['modalfiles'])){
            foreach ($this->field['modalfiles'] as $key => $value) {
                $this->field['existfiles'][$key] = $value;
            }
        }
    }

    // get file content
    public function fileContent($path) {
        return file_get_contents($path);
    }

	// Table Field Array
	public function tableFieldsLoop($request) {
        $fillable_lines = "";
        $formelement = "";
        
        for($i=0; $i < count($request->name); $i++) {

            // [FILLEBLE_LINES]
            $fillable_lines .= "'".strtolower($request->name[$i])."',"."\n"."\t"."\t"."\t"."\t";

            // [FORMELEMENT_LINES]
            $formelement .= '"'.$request->name[$i].'" => '.'[],'."\n"."\t"."\t"."\t";
        }

        $this->field['fillable_lines'] = $fillable_lines;
        $this->field['formelement'] = $formelement;
	}

	// Input Field Array
	public function inputFieldsLoop($request, $project_path_main, $parent_controller_name) {
        $options_data = ''; 
        $dropdown_mount = '';
        $module_data = "";
        $modal_path = '';
        $grid_element = "";
        $dropdown_common_function = "";
        $common_route = "";
        $dropdown_search_url = '';
        $grid_edit_data = '';
        $grid_edit_empty_data = '';
        $grid_activity = '';
        $empty_drop_array = "";

		for($i=0; $i < count($request->input_name); $i++) {
            if($request->visible[$i]){

                // $name = explode("|", $request->input_name[$i]);
                $input_name = $request->input_name[$i];
                $db_name = $request->db_name[$i];

                $grid_element .= $this->gridArrayElement($request, $i, $input_name, $db_name);

                $grid_edit_data .= $this->gridEditElement($request, $i, $db_name);

                $grid_edit_empty_data .= $this->gridEditEmptyElement($request, $i, $db_name);

                $grid_activity .= $this->gridActivity($request, $i, $db_name);

                if($request->input_type[$i] == 'dropdown'){
                    if($request->key[$i] != "") {
                        //[OptionsData]
                        $options_data .= $this->optionDataDropdown($db_name);

                        //[DropdownSearch]
                        $dropdown_mount .= $this->dropdownMount($db_name, $request->table[$i]);

                        //[DROP_MODULE]
                        $this->makeModal($request->table[$i], $project_path_main);

                        // [Modal_path]
                        $modal_path .= $this->modalPath($request->table[$i]);

                        // [Module_Data]
                        $module_data .= $this->moduleData($request->table[$i]);

                        // common function [Function]
                        $dropdown_common_function .= $this->makeCommon($request, $i, $db_name, $project_path_main);

                        // common route [CommonRoute]
                        $common_route .= $this->commonRoute($request, $i, $db_name); 

                        //drop_search in append moduleconfig array
                        $dropdown_search_url .= $this->makeDropdown($db_name);
                    } else {
                        //[OptionsData] for empty dropdown
                        $options_data .= $this->optionDataEmptyDropdown($db_name);

                        // [EmptyDropDown]
                        $empty_drop_array .= $this->emptyDropArray($db_name);
                    }
                }
            }
		}

        $this->field['options_data'] = $options_data.'// [GridOptionsData]';
        $this->field['empty_drop'] = $empty_drop_array;
        $this->field['dropdown_mount'] = $dropdown_mount.'// [GridDropdownSearch]';
        $this->field['modal_path'] = $modal_path."\n".'{{-- [Modal_path] --}}';
        $this->field['module_data'] = $module_data.'// [Module_Data]';

        $this->field['grid_element'] = $this->GridElement($request, $grid_element, $parent_controller_name);

        $this->field['dropdown_common_function'] = $dropdown_common_function;
        $this->field['common_route'] = $common_route;
        $this->field['dropdown_search_url'] = $dropdown_search_url."\n"."\t"."\t"."\t"."\t".'// ['.ucfirst($parent_controller_name).'Module]';
        $this->field['grid_edit_data'] = $grid_edit_data;
        $this->field['grid_edit_empty_data'] = $grid_edit_empty_data;
        $this->field['grid_activity'] = $grid_activity;
	}

    // grid element array for module config
    public function GridElement($request, $grid_element, $parent_controller_name) {
        $this->field['empty_drop'];
        return '$data["'.$request->table_name.'"]'." = "."["."\n"."\t"."\t"."\t"."\t". $grid_element."\n"."\t"."\t"."\t"."];"."\n"."\t"."\t"."\t".'$data["'.$request->table_name.'row_count"] = 0;'."\n"."\t"."\t"."\t".'$data["'.$request->table_name.'_row"][] = 0;'."\n"."\t"."\t".$this->field['empty_drop']."\n"."\t"."\t".'// ['.ucfirst($parent_controller_name).'Grid]';
    }

    // common fuction [Function]
    public function makeCommon($request, $i, $db_name, $project_path_main) {
        $content = $this->fileContent($project_path_main."/app/Http/Controllers/Backend/CommonController.php");
        if(!strpos($content, "get".ucfirst($db_name))){
            return "\n"."\t"."public function get".ucfirst($db_name)."(".'Request $request'.") {
                \n"."\t"."\t"."return \DB::table('".$request->table[$i]."')->latest()->wherenull('deleted_at')->pluck('".$request->key[$i]."','".$request->value[$i]."');
            \n"."\t"."}";
        }
    }

    // common route [CommonRoute]
    public function commonRoute($request, $i, $db_name) {
        
        $route =  base_path()."/vendor/ongoingcloud/laravelcrud/Vuesample/common.php";
            
        $common = file_get_contents($route);
        
        $common = preg_replace('/\\['.preg_quote('MODULE','/').'\\]/',strtolower($db_name),$common);
        $common = preg_replace('/\\['.preg_quote('UMODULE','/').'\\]/',ucfirst($db_name),$common);

        return $common;
    }

    // grid element array for controller
    public function gridArrayElement($request, $i, $input_name, $db_name) {
        $this->field['grid_html'] = "\n"."\t"."\t"."\t".'<div class="col-sm-12">'."\n"."\t"."\t"."\t"."\t".'
                <h5>'.ucwords($request->main_module).'</h5>'."\n"."\t"."\t"."\t".'
            </div>'."\n"."\t"."\t"."\t".
            '<grid :module="this.module" :elementdata="this.module.'.$request->table_name.'" :elementrow="this.module.'.$request->table_name.'_row" :rowcount="this.module.'.$request->table_name.'row_count" ref="'.$request->table_name.'"></grid>'."\n"."\t"."\t".'<!-- [GridVueElement-1] -->';
        
        $uname = "'".ucfirst($input_name)."'";

        if($request->input_type[$i] == 'dropdown') {
            if($request->key[$i] != "") {
                return $uname." => ["."\n"."\t"."\t"."\t"."\t"."\t"."'type' => '".$request->input_type[$i]."',"."\n"."\t"."\t"."\t"."\t"."\t"
                      ."'name' => '".$db_name."',\n"."\t"."\t"."\t"."\t"."\t"
                      ."'modalid' => '#".$request->table[$i]."Modal',\n"."\t"."\t"."\t"."\t"."],"."\n"."\t"."\t"."\t"."\t"; 
            } else {
                return $uname." => ["."\n"."\t"."\t"."\t"."\t"."\t"."'type' => '".$request->input_type[$i]."',"."\n"."\t"."\t"."\t"."\t"."\t"
                      ."'name' => '".$db_name."',\n"."\t"."\t"."\t"."\t"."\t"
                      ."'empty' => true,\n"."\t"."\t"."\t"."\t"."],"."\n"."\t"."\t"."\t"."\t"; 
            }
        } else {
            return $uname." => ["."\n"."\t"."\t"."\t"."\t"."\t"."'type' => '".$request->input_type[$i]."',"."\n"."\t"."\t"."\t"."\t"."\t"
                  ."'name' => '".$db_name."',\n"."\t"."\t"."\t"."\t"."],"."\n"."\t"."\t"."\t"."\t"; 
        }       
    }

    // grid activity 
    public function gridActivity($request, $i, $db_name) {
        
        if($request->input_type[$i] == 'date'){
            return '$input_grid["'.$db_name.'"][] = Helpers::parseDBdate($request->'.$db_name.'[$i]);'."\n"."\t"."\t"."\t"."\t"."\t";
        }
        if($request->input_type[$i] == 'dropdown') {
            return '$input_grid["'.$db_name.'"][] = $request->'.$db_name.'[$i];'."\n"."\t"."\t"."\t"."\t"."\t";
        }
        if($request->input_type[$i] == 'input'){
            return '$input_grid["'.$db_name.'"][] = $request->'.$db_name.'[$i];'."\n"."\t"."\t"."\t"."\t"."\t";
        } 
    }

    // complete function and array of grid array
    public function gridActivityArray($request, $parent_controller_name) {
        $element = isset($request->db_name[1]) && $request->visible[1] ? $request->db_name[1] : $request->db_name[0];

        // $this->field['grid_activity']
        $grid_activity = '$input_grid = [];'."\n"."\t"."\t"."\t"."\t";
        $grid_activity .=  'for($i=0; $i < count(array_filter($request->'.$element.')); $i++) {'."\n"."\t"."\t"."\t"."\t"."\t";
        $grid_activity .= '$input_grid["'.$parent_controller_name.'_id"][]'.' ='.' $model->id;'."\n"."\t"."\t"."\t"."\t"."\t"; 
        $grid_activity .= $this->field['grid_activity'];

        $grid_activity .= "\n"."\t"."\t"."\t"."\t".'}'."\n"."\t"."\t"."\t"."\t";

        $grid_activity .= '$msg_row = Helpers::activityRow($input_grid, count(array_filter($request->'.$element.')), $model->'.$request->table_name.'->toArray());
                foreach ($msg_row as $key => $value) {
                    Activity::add($value, $this->data["dir"], $model->id);
                }';
        return $grid_activity."\n"."\t"."\t"."\t"."\t".'// [GridActivity]';
    }

    // grid validation method [GridValidation]
    public function gridValidation($request) {
        $element = isset($request->db_name[1]) && $request->visible[1] ? $request->db_name[1] : $request->db_name[0];

        $grid_validation =  '$array = [];'."\n"."\t"."\t";

        $grid_validation .=  '$rows = count($request->'.$element.');'."\n"."\t"."\t";

        $grid_validation .= 'for ($i = 0; $i < $rows; $i++) {'."\n"."\t"."\t"."\t";
        
        $i = 0;

        foreach ($request->input_name as  $value) {

            if($request->visible[$i]) {

                $array = '$array["'.$value.'"]';
                if($request->input_type[$i] == 'date'){
                    $grid_validation .= "$array = ".'Helpers::parseDBdate($request->'.$request->db_name[$i].'[$i]'.");"."\n"."\t"."\t"."\t";
                }
                if($request->input_type[$i] == 'dropdown') {
                    $grid_validation .= "$array = ".'$request->'.$request->db_name[$i].'[$i];'."\n"."\t"."\t"."\t";
                }
                if($request->input_type[$i] == 'input'){
                    $grid_validation .= "$array = ".'$request->'.$request->db_name[$i].'[$i];'."\n"."\t"."\t"."\t";
                }
            }
            $i++;  
        }
        $grid_validation .= 'if(count(array_filter($array)) != 0) {
                foreach ($array as $key => $value) {
                    if($value == ""){
                        return Helpers::errorResponse(ucfirst($key)." Field is required");
                    }
                }
            } else {
                if($rows == 1) {
                    return Helpers::errorResponse("Add '.strtolower($request->main_module).'. Atleast one row is required");
                }
            }
        }'."\n"."\t"."\t".'// [GridValidation]';
        return $grid_validation;
    }

    // grid save method [GridSave]
    public function gridSave($request, $parent_controller_name) {
        $element = isset($request->db_name[1]) && $request->visible[1] ? $request->db_name[1] : $request->db_name[0];

        $grid_save =  '
            for($i=0; $i < count(array_filter($request->'.$element.')); $i++) {'."\n"."\t"."\t"."\t"."\t"."\t";
        
        $grid_save .= '$model->'.$request->table_name."()->create(["."\n"."\t"."\t"."\t"."\t"."\t"."\t"."\t"."\t";
        $grid_save .= '"'.$parent_controller_name.'_id"'.' =>'.' $model->id,'."\n"."\t"."\t"."\t"."\t"."\t"."\t"."\t"."\t"; 
        $i = 0;

        foreach ($request->input_name as  $value) {
            if($request->visible[$i]) {
                $element = $request->db_name[$i];
                
                if($request->input_type[$i] == 'date'){
                    $grid_save .= "'$element' => ".'Helpers::parseDBdate($request->'.$element.'[$i]'."),"."\n"."\t"."\t"."\t"."\t"."\t"."\t"."\t"."\t";
                }
                if($request->input_type[$i] == 'dropdown') {
                    $grid_save .= "'$element' => ".'$request->'.$element.'[$i],'."\n"."\t"."\t"."\t"."\t"."\t"."\t"."\t"."\t";    
                }
                if($request->input_type[$i] == 'input'){
                    $grid_save .= "'$element' => ".'$request->'.$element.'[$i],'."\n"."\t"."\t"."\t"."\t"."\t"."\t"."\t"."\t";
                }
            }
            $i++; 
        } 
        $grid_save .= "\n"."\t"."\t"."\t"."\t"."\t".']);'."\n"."\t"."\t"."\t"."\t".'}';

        return $grid_save."\n"."\t"."\t"."\t"."\t".'// [GridSave]';
    }

    // grid delete method [GridDelete]
    public function gridDelete($request, $parent_controller_name) {
        
        return '$model->'.$request->table_name.'()->where("'.$parent_controller_name.'_id", $request->id)->delete();'."\n"."\t"."\t"."\t"."\t".'// [GridDelete]';
    }

    public function gridEditElement($request, $i, $db_name) {

        $selected_variable = "['".$db_name."'][]";
        $model = '$value->'.$db_name;

        if($request->input_type[$i] == 'date'){
            return '$formelement'.$selected_variable .' = '. 'Helpers::parseDate('.$model.');'."\n"."\t"."\t"."\t"."\t";
        }
        if($request->input_type[$i] == 'dropdown') {
            return '$formelement'.$selected_variable .' = '.$model.';'."\n"."\t"."\t"."\t"."\t";
        }
        if($request->input_type[$i] == 'input'){
            return '$formelement'.$selected_variable .' = '.$model.';'."\n"."\t"."\t"."\t"."\t";
        }
    }

    public function gridEditEmptyElement($request, $i, $db_name) {

        $selected_variable = "['".$db_name."'][]";
        $model = '$value->'.$db_name;

        if($request->input_type[$i] == 'date'){
            return '$formelement'.$selected_variable .' = "";'."\n"."\t"."\t"."\t";
        }
        if($request->input_type[$i] == 'dropdown') {
            return '$formelement'.$selected_variable .' = "";'."\n"."\t"."\t"."\t";
        }
        if($request->input_type[$i] == 'input'){
            return '$formelement'.$selected_variable .' = "";'."\n"."\t"."\t"."\t";
        }
    }
    public function gridEdit($request) {
      
        return 
        'if(count($model->'.$request->table_name.') > 0 ) {
            $this->data["'.$request->table_name.'_row"] = [];'."\n"."\t"."\t"."\t".
        
            '$this->data["'.$request->table_name.'row_count"] = count($model->'.$request->table_name.') - 1;'."\n"."\t"."\t"."\t".
            'foreach ($model->'.$request->table_name.' as $key => $value) {'."\n"."\t"."\t"."\t"."\t".
                '$this->data["'.$request->table_name.'_row"][] = $key;'."\n"."\t"."\t"."\t"."\t".

                $this->field['grid_edit_data']."\n"."\t"."\t"."\t".
            '}'."\n"."\t"."\t".
        '} else {'."\n"."\t"."\t"."\t".
            $this->field['grid_edit_empty_data']."\n"."\t"."\t".
        '}'."\n"."\t"."\t".'// [GridEdit]';
    }

    //drop_search in append moduleconfig array
    public function makeDropdown($db_name){
        
        $name = "get.".$db_name;           
        return "'".$db_name."_search'"." => route('".$name."'),"."\n"."\t"."\t"."\t"."\t";
    }

    // [GridOptionsData]
    public function optionDataDropdown($db_name){
        $content = $this->common_grid_vue;

        if(!strpos($content, $db_name." : [],")) {
            return $db_name." : [],"."\n"."\t"."\t"."\t";
        }
    }

    // [OptionsData] for empty dropdown
    public function optionDataEmptyDropdown($db_name){
        $content = $this->common_grid_vue;

        if(!strpos($content, $db_name." : this.module.".$db_name.",")) {
            return $db_name." : this.module.".$db_name.","."\n"."\t"."\t"."\t";
        }
    }

    // [EmptyDropDown]
    public function emptyDropArray($db_name){

        return '$data["'.$db_name.'"] = [];'."\n"."\t"."\t"."\t";
    }

    // [GridDropdownSearch]
    public function dropdownMount($db_name, $table_name){
        $url = "this.module.".$db_name."_search";

        $content = $this->common_grid_vue;

        if(!strpos($content, "if($url) {")) {
        return "
        if($url) {
            axios.get($url).then(data => {
                this.$db_name = data.data;
            });
        }
        ";
        }
    }

    // [DROP_MODULE]
    public function makeModal($table_name, $project_path_main) {
       
        $content = $this->sample_modal;
        $content = preg_replace('/\\['.preg_quote('DROP_MODULE','/').'\\]/',strtolower($table_name),$content);
        $view_folder = $project_path_main.'/resources/views/backend/modal';
        $this->field['modalfiles'][$view_folder.'/'.strtolower($table_name).'.blade.php'] = $content;
    }

    // {{-- [Modal_path] --}}
    public function modalPath($table_name) {
        $data = '$data'."['".$table_name."_module']";
        return '@include("backend.modal.'.$table_name.'",'.$data.')'."\n";
    }

    // [Module_Data]
    public function moduleData($table_name) {
        
        $input_name_array = "['".$table_name."_module']";
        return '$this->data'. $input_name_array. ' = ' .'ModuleConfig::'.$table_name.'();'."\n"."\t"."\t";
    }
}
