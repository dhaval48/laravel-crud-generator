<?php

namespace Ongoingcloud\Laravelcrud\General;

use Ongoingcloud\Laravelcrud\Helpers;

Class Helper {

    public $sample_controller;
    public $sample_component;
    public $sample_datamigration;
    public $sample_html;
    public $sample_list;
    public $sample_listvue;
    public $sample_migration;
    public $sample_modal;
    public $sample_model;
    public $sample_route;
    public $sample_side;
    public $sample_view;
    public $sample_vue;
    public $sample_request;
    public $belongsTo;
    public $module_test_case;

    public $field = [];

    function __construct() {
        $this->sample_controller = base_path()."/vendor/ongoingcloud/laravelcrud/Vuesample/Controller.php";
        $this->sample_component = base_path()."/vendor/ongoingcloud/laravelcrud/Vuesample/component.js";
        $this->sample_datamigration = base_path()."/vendor/ongoingcloud/laravelcrud/Vuesample/datamigration.php";
        $this->sample_html = base_path()."/vendor/ongoingcloud/laravelcrud/Vuesample/html.vue";
        $this->sample_list = base_path()."/vendor/ongoingcloud/laravelcrud/Vuesample/list.php";
        $this->sample_listvue = base_path()."/vendor/ongoingcloud/laravelcrud/Vuesample/list.vue";
        $this->sample_request = base_path()."/vendor/ongoingcloud/laravelcrud/Vuesample/Request.php";

        $this->sample_modal = base_path()."/vendor/ongoingcloud/laravelcrud/Vuesample/modal.php";
        $this->sample_model = base_path()."/vendor/ongoingcloud/laravelcrud/Vuesample/Model.php";
        $this->sample_route = base_path()."/vendor/ongoingcloud/laravelcrud/Vuesample/Route.php";
        $this->sample_side = base_path()."/vendor/ongoingcloud/laravelcrud/Vuesample/side.php";
        $this->sample_view = base_path()."/vendor/ongoingcloud/laravelcrud/Vuesample/view.php";
        $this->sample_vue = base_path()."/vendor/ongoingcloud/laravelcrud/Vuesample/view.vue";
        $this->belongsTo = base_path()."/vendor/ongoingcloud/laravelcrud/Vuesample/belongsTo.php";
        $this->module_test_case = base_path()."/vendor/ongoingcloud/laravelcrud/Vuesample/ModuleTestCase.php";
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
        
        $table_fields = $this->getTableFields($request, $old_data);

        if(empty($request->parent_module)) {
            $this->sample_route = base_path()."/vendor/ongoingcloud/laravelcrud/Vuesample/WithoutPrefixRoute.php";
        }

        if(!empty($this->field['table_fields'])) {
            $this->sample_migration = base_path()."/vendor/ongoingcloud/laravelcrud/Vuesample/UpdateMigration.php";
        } else {
            if(!empty($request->parent_module)) {
                $this->sample_migration = base_path()."/vendor/ongoingcloud/laravelcrud/Vuesample/Migration.php";
            } else {
                $this->sample_migration = base_path()."/vendor/ongoingcloud/laravelcrud/Vuesample/WithoutModuleMigration.php";
            }
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

        $this->inputFieldsLoop($request, $project_path_main, $controller_name);
                
        $table_fields = $this->getTableFields($request, $old_data);
        if(!empty($this->field['table_fields'])) {
            $table_fields = $this->field['table_fields'];
        }

        $class_name = $this->makeClassName($request);

        $random = rand();
        $this->field['migration_file_name'] = $random."_".$request->table_name;

        $parent_module = $request->parent_module;

        $replace_word = [
                        'MODULE' => strtolower($controller_name),
                        'UMODULE' => ucfirst($controller_name),
                        'ULABEL' => ucwords($request->main_module),
                        'TNAME' => strtolower($request->table_name),
                        'LNAME' => strtolower($controller_name),
                        'UNAME' => ucfirst($controller_name),
                        'VALIDATION' => $this->field['validation'],
                        'TESTCASEDATA' => $this->field['test_case_data'],
                        'LMODULE' => strtolower($controller_name),
                        'FORM_FIELDS' => $this->field['form_fields'],
                        'TABLE_NAME' => strtolower($request->table_name),
                        'OptionsData'   => $this->field['options_data'],

                        'MODULENAME' => strtolower($controller_name),
                        'DropdownSearch' => $this->field['dropdown_mount'],
                        'CLASS_MODULE' => $class_name,
                        'TABLE_FIELDS' => $table_fields,
                        'CLASS_UPDATE_MODULE' => $random."".$class_name,
                        'PAMODULE' => ucfirst($parent_module),
                        'LAMODULE' => trim(strtolower(str_replace(' ','-',$parent_module))),
                        'PMODULE' => ucfirst($parent_module),
                        'Module_Data' => $this->field['module_data'],
                        'DropdownValue' => $this->field['dropdown_value'],
                        'DropdownSelectedValue' => $this->field['dropdown_selected_value'],
                        'Modal_path' => $this->field['modal_path'],

                        'FILLEBLE_LINES' => $this->field['fillable_lines'],
                        'FORMELEMENT_LINES' => $this->field['formelement'],
                        'SEARCHELEMENT_LINES' => $this->field['searchelement'],
                        'LIST_DATA_LINES' => $this->field['list_data'],
                        'CFOLDER' => ucfirst($controller_name),

                        'OUTPUT' => "php://output",
                        'GridSave' => "// [GridSave]",
                        'GridEdit' => "// [GridEdit]",
                        'GridActivity' => "// [GridActivity]",
                        'GridValidation' => "// [GridValidation]",
                        'GridDelete' => "// [GridDelete]",
                        'ModelArray' => "// [ModelArray]",
                        'Relation' => $this->field['relation'],
                        'GRID_RESET' => "// [GRID_RESET]",
                        'POST_METHOD' => $this->checkPostMethod($request),
                        'Controller_Relation' => $this->field['controller_relation'],
                        'Controller_Search_Relation' => $this->field['controller_search_relation'],
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
        $this->field['db_module_config'] = $this->ModuleConfig($request, $controller_name);

        $this->getAllFiles($request, $project_path_main, $controller_name, $old_data);

        $this->makerequest($request, $project_path_main, $controller_name);
        
        $this->putContentDhaval($request, $project_path_main, $this->field['deletefiles']);

        // replace word and put content in existing file
        $project_replace_word = [
                                "// [RouteArray]" => $this->sample_route."\n"."// [RouteArray]",
                                "// [Function]" => $this->field['dropdown_common_function']."\n"."\t".'// [Function]',
                                "// [CommonRoute]" => $this->field['common_route']."\n"."// [CommonRoute]",
                                "// [VueComponent]" => $this->sample_component."\n"."// [VueComponent]",
                                // "{{-- [dashboard_link] --}}" => $this->sample_side."\n"."\t"."\t"."\t".'{{-- [dashboard_link] --}}',
                                "// [Moduleconfig]" => $this->field['db_module_config']."\n"."\t".'// [Moduleconfig]',
                            ];
        
        foreach ($this->field['files'] as $key => $value) {

            foreach ($project_replace_word as $module_key => $module_value) {

                $value = preg_replace('/'.preg_quote($module_key,'/').'/',$module_value, $value);
                $value = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n"."\n", $value);
                file_put_contents($key, $value);
            }
        }
    }

    public function checkPostMethod($request){
        $method = 'this.form.post(this.module.store_route).then(response => {';
        if($this->checkFileElement($request)) {
            $method = `
                var data = new FormData($("form")[0]);"\n\n"
                this.form.postWithFile(this.module.store_route,data).then(response => {
                `;
        }

        return $method;
    }

    public function checkFileElement($request){
        for($i=0; $i < count($request->input_name); $i++) {
            if($request->input_type[$i] == "file") {
                return true;
            }
        }
        return false;
    }

    public function putContentDhaval($request, $project_path_main, $files) {

        foreach ($files as $key => $value) {
            if(file_exists($key)) {
                unlink($key);
            }
            file_put_contents($key, $value);
        }
    }

    // New folder create
    public function createFolder($request, $filePath) {
        if(!is_dir($filePath)){
            mkdir($filePath);                            
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


    public function getAllFiles($request, $project_path_main, $controller_name, $old_data) {
        $vueFolder = $project_path_main.'/resources/js/components/backend/'.strtolower($controller_name);
        $this->createFolder($request, $vueFolder);

        $migration_path = $this->getMigrationPath($request, $project_path_main, $old_data);        

        $this->field['deletefiles'] = [

                //Whole file change
                $project_path_main."/app/Http/Controllers/Backend/".ucfirst($controller_name).'Controller.php' => $this->sample_controller,

                $project_path_main."/app/Models/".ucfirst($controller_name).'.php' => $this->sample_model,

                $project_path_main."/resources/views/backend/modules/".strtolower($controller_name).'-table.blade.php' => $this->sample_list,

                $project_path_main."/resources/views/backend/modules/".strtolower($controller_name).'.blade.php' => $this->sample_view,
                
                $vueFolder.'/html.vue' => $this->sample_html ,

                $vueFolder.'/view.vue' => $this->sample_vue,

                $vueFolder.'/list.vue' => $this->sample_listvue,

                $project_path_main."/app/General/ModuleConfig/".strtolower($controller_name).'.php' => $this->field['moduleconfig_filedata'],

                $project_path_main."/resources/lang/en/".strtolower($request->table_name).".php" => $this->field['lang'],

                $project_path_main."/tests/Browser/Backend/".ucfirst($controller_name).'Test.php' => $this->module_test_case,

            ];

        if(!empty($this->field['table_fields'])) {
            $this->field['deletefiles'][$migration_path] = $this->sample_migration;
        } else {
            if($old_data == "") {
                $this->field['deletefiles'][$migration_path] = $this->sample_migration;
            }
        }

        if(isset($this->field['modalfiles'])){
            foreach ($this->field['modalfiles'] as $key => $value) {
                $this->field['deletefiles'][$key] = $value;
            }
        }
               
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

    // get file content
    public function fileContent($path) {
        return file_get_contents($path);
    }

    // make request file
    private function makerequest($request, $project_path_main, $controller_name) {
        
        $request_folder = $project_path_main."/app/Http/Requests".'/'.ucfirst($controller_name);
        $this->createFolder($request, $request_folder);

        $content = $this->sample_request;
        
        $default_content = $content;
        $actions = ['Store','List','Update','Delete','Only'];

        foreach ($actions as $key => $value) {
            $content = preg_replace('/\\['.preg_quote('URACTION','/').'\\]/',$value,$content);
            $content = preg_replace('/\\['.preg_quote('LRACTION','/').'\\]/',strtolower($value),$content);
            
            $this->field['deletefiles'][$request_folder.'/'.$value.ucfirst($controller_name).'Request.php'] = $content;
            $content = $this->sample_request;
        }
    }

    // Table Field Array
    public function tableFieldsLoop($request) {
        $fillable_lines = "";
        $formelement = "";
        $list_data = "";
        $searchelement = "";

        //[VALIDATION]
        $this->getValidation($request);
        
        for($i=0; $i < count($request->name); $i++) {
            // [FILLEBLE_LINES]
            $fillable_lines .= $this->prepareFillable($request->name[$i]);

            if($request->visible[$i]) {
                // [FORMELEMENT_LINES]
                $formelement .= $this->formElement($request, $request->name[$i]);
            }

            // [LIST_DATA_LINES]
            $list_data .= $this->listData($request, $request->name[$i], $i);

            // [SEARCHELEMENT_LINES]
            $searchelement .= $this->prepareFillable($request->name[$i]);
        
        }

        $this->field['fillable_lines'] = $fillable_lines;
        $this->field['formelement'] = $formelement;
        $this->field['list_data'] = $list_data;
        $this->field['searchelement'] = $searchelement;
    }

    // Input Field Array
    public function inputFieldsLoop($request, $project_path_main, $controller_name) {

        $form_fields = '';
        $dropdown_search_url = '';
        $options_data = ''; 
        $dropdown_mount = '';
        $modal_path = '';
        $module_data = "";
        $dropdown_value = "";
        $dropdown_selected_value = "";
        $lang = "";
        $dropdown_common_function = "";
        $common_route = "";
        $model_array = "";
        $empty_drop_array = "";
        $relation = "";
        $this->field['controller_relation'] = "";
        $this->field['controller_search_relation'] = "";
        $this->field['test_case_data'] = "";

        $count = 0;
        for($i=0; $i < count($request->input_name); $i++) {

            // $name = explode("|", $request->input_name[$i]);
            $input_name = $request->input_name[$i];
            $db_name = $request->db_name[$i];

            // [LangArray]
            $lang .= "'".$db_name."' => "."'".ucwords($input_name)."',"."\n"."\t"."\t";

            if($request->visible[$i]){
                //[FORM_FIELDS]
                $form_fields = $this->getFormFields($request, $i, $form_fields, $input_name, $db_name, $count);

                if($request->input_type[$i] == 'checkbox') {
                    // [DropdownValue]
                    $dropdown_value .= $this->checkboxValue($db_name);
                }

                if($request->input_type[$i] == 'dropdown'){
                    //[DropdownSearch]
                    $dropdown_mount .= "\n"."\t"."\t"."this.form.$db_name = this.form.$db_name ? this.form.$db_name : '';"."\n"."\t"."\t";
                    $dropdown_mount .= $this->dropdownMountEmpty($db_name, $request->table[$i], $dropdown_mount);

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

                        //drop_search in append moduleconfig array
                        $dropdown_search_url .= $this->makeDropdown($db_name);

                        // common function [Function]
                        $dropdown_common_function .= $this->makeCommon($request, $i, $db_name, $project_path_main);

                        // [Relation]
                        $relation .= $this->makeRelation($request, $i, $db_name);

                        // common route [CommonRoute]
                        $common_route .= $this->commonRoute($request, $i, $db_name); 
                    } else {
                        //[OptionsData] for empty dropdown
                        $options_data .= $this->optionDataEmptyDropdown($db_name);

                        // [EmptyDropDown]
                        $empty_drop_array .= $this->emptyDropArray($db_name, $project_path_main, $controller_name);
                    }
                }
                if($request->input_type[$i] == 'date') {
                    // [DropdownValue] use for append date format
                    $dropdown_value .= $this->addParseDBDate($db_name);
                    $dropdown_selected_value .= $this->addParseDate($db_name);
                }
                $count++;
                if($count == 2) {
                    $count = 0;
                }
            }
        }
        // dd($this->field['test_case_data']);
        $this->field['form_fields'] = $form_fields;
        
        $this->field['options_data'] = $options_data.'// [OptionsData]';
        $this->field['empty_drop'] = $empty_drop_array;

        $this->field['dropdown_mount'] = $dropdown_mount.'// [DropdownSearch]';
        $this->field['modal_path'] = $modal_path."\n".'{{-- [Modal_path] --}}';
        $this->field['module_data'] = $module_data.'// [Module_Data]';
        $this->field['dropdown_value'] = $dropdown_value.'//[DropdownValue]';
        $this->field['dropdown_selected_value'] = $dropdown_selected_value.'// [DropdownSelectedValue]';
        $this->field['lang'] = $this->lang($lang,$request);
        $this->field['model_array'] = $model_array.'// [ModelArray]';
        $this->field['dropdown_common_function'] = $dropdown_common_function;
        $this->field['relation'] = $relation.'// [Relation]';
        if(!empty($this->field['controller_relation'])) {
            $this->field['controller_relation'] = "Module::latest()->with(".$this->field['controller_relation'].")";
        } else {
            $this->field['controller_relation'] = "Module::latest()";
        }

        if(!empty($this->field['controller_search_relation'])) {
            $this->field['controller_search_relation'] = $this->field['controller_search_relation'].";";
        } else {
            $this->field['controller_search_relation'] = ";";
        }

        $this->field['common_route'] = $common_route;

        $this->field['dropdown_search_url'] = $dropdown_search_url."\n"."\t"."\t"."\t"."\t".'// ['.ucfirst($controller_name).'Module]';
        // dd($this->field['dropdown_search_url']);
    }

    // array content [Moduleconfig]
    private function ModuleConfig($request, $controller_name) {
        $lang = "('".strtolower($request->table_name)."')";

        $formelements = '$formelements'. ' = '.'['. $this->field['formelement']."\n"."\t"."\t".'// ['.ucfirst($controller_name)."Array]\n"."\t"."\t".'];';
        $csrf = '$formelements["_token"]'. ' = ' .'csrf_token();';

        $empty_drop = $this->field['empty_drop'];

        $this->field['moduleconfig_filedata'] = "
        <?php

        $formelements\n"."\t"."\t"."
        $csrf\n"."\t"."\t".
        
        '$data'." =  [
                'lang' => Lang::get".$lang.",
                'common' => Lang::get('label.common'),
                
                'dir'  => '".strtolower($controller_name)."',
                'id' => 0,
                'is_visible' => true,
                
                'list_route'  =>  route('".strtolower($controller_name).".index'),
                'store_route'  => route('".strtolower($controller_name).".store'),
                'paginate_route'  => route('".strtolower($controller_name).".paginate'),
                'edit_route'  => route('".strtolower($controller_name).".edit',''),
                'create_route'  => route('".strtolower($controller_name).".create'),
                'destory_route' => route('".strtolower($controller_name).".destroy'),
                'get_activity' => route('get.activity'),
                'get_file' => route('get.file'),
                ".$this->field['dropdown_search_url']."
            ];"."\n"."\t"."\t".
        '$data["fillable"]'." = ".'$formelements'.";"."\n"."\t"."\t".
        "$empty_drop"."\n"."\t"."\t".
        '// ['.ucfirst($controller_name).'Grid]'."\n"."\t"."\t".
        'return $data;'."\n"."
        ";

        return "
    public static function ".strtolower($request->table_name)."() {
        return include 'ModuleConfig/".strtolower($controller_name).".php';
    }";

    }


    // common fuction [Function]
    public function makeCommon($request, $i, $db_name, $project_path_main) {
        $content = $this->fileContent($project_path_main."/app/Http/Controllers/Backend/CommonController.php");
        if(!strpos($content, "get".ucfirst($db_name))) {
            return "\n"."\t"."public function get".ucfirst($db_name)."(".'Request $request'.") {
                \n"."\t"."\t"."return \DB::table('".$request->table[$i]."')->latest()->wherenull('deleted_at')->pluck('".$request->key[$i]."','".$request->value[$i]."');
            \n"."\t"."}";
        }
    }

    // [Relation]
    public function makeRelation($request, $i, $db_name) {
        $main_module = \DB::table('form_modules')->where('table_name', $request->table[$i])->wherenull('deleted_at')->wherenull('parent_form')->first();

        if($main_module) {
            $controller_name = $this->makeControllerName($main_module);
        } else {
            $controller_name = ucfirst($request->table[$i]);
        }

        if(!empty($this->field['controller_relation'])) {
            $this->field['controller_relation'] .= ',';
        }
        $this->field['controller_relation'] .= '"'.strtolower($request->table[$i]).'"';

        $this->field['controller_search_relation'] .= '->orwhereHas("'.strtolower($request->table[$i]).'",function($query) use ($request){
                                    $query->where("'.$request->key[$i].'","like","%$request->q%");
                                })';
        
        $content = $this->belongsTo;
        $content = preg_replace('/\\['.preg_quote('TNAME','/').'\\]/',strtolower($request->table[$i]),$content);
        $content = preg_replace('/\\['.preg_quote('UMODULE','/').'\\]/',ucfirst($controller_name),$content);
        $content = preg_replace('/\\['.preg_quote('PMODULE','/').'\\]/',strtolower($db_name),$content);
        
        return $content."\n";        
    }

    // common route [CommonRoute]
    public function commonRoute($request, $i, $db_name) {
        
        $route =  base_path()."/vendor/ongoingcloud/laravelcrud/Vuesample/common.php";
            
        $common = file_get_contents($route);
        
        $common = preg_replace('/\\['.preg_quote('MODULE','/').'\\]/',strtolower($db_name),$common);
        $common = preg_replace('/\\['.preg_quote('UMODULE','/').'\\]/',ucfirst($db_name),$common);

        return $common;
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

    //[FORM_FIELDS] 
    public function getFormFields($request, $i, $form_fields, $input_name, $db_name, $count) {
        if($db_name != 'id') {
            // if($count == 0) {
            //     $form_fields .= "\n"."\t"."\t"."\t".'<div class="row">';
            // }

            $first = 'form.errors.has("'.$db_name.'")?"form-group has-error":"form-group"';
            $v_model = "form.$db_name";
            $error = "$db_name-error";
            $error_if = 'form.errors.has("'.$db_name.'")';
            $error_end = 'form.errors.get("'.$db_name.'")';

            if($request->input_type[$i] == "dropdown") {
        
                $model = "form.".$db_name;
                $placeholder = "Select ".$input_name;
                $false = 'false';
                $search = "onSearch".$db_name;
                $modal = "#".$request->table[$i]."Modal";

        if($request->key[$i] != "") {
                $form_fields .= 
                "
                <div class='col-sm-6'>
                    <div :class='".$first."'>
                        <label for='".$input_name."'> {{this.module.lang.".$db_name."}} </label>
                        
                        <select class='form-control select-form' ref='".$db_name."' name='".$db_name."' v-model='".$model."'>
                            <option value=''>".$placeholder."</option>
                            <option v-for='(value, key) in ".$db_name."' :value='key'>{{value}}</option>
                        </select>

                        <span class='help-block text-danger' 
                        v-if='".$error_if."'
                        v-text='".$error_end."'></span>
                    </div>
                </div>
                ";

            $this->field['test_case_data'] .= "->select('".$db_name."',1)"."\n"."\t"."\t"."\t"."\t"."\t";

        } else {
            $form_fields .= 
                "
                <div class='col-sm-6'>
                    <div :class='".$first."'>
                        <label for='".$input_name."'>{{this.module.lang.".$db_name."}}</label>

                        <select class='form-control select-form' ref='".$db_name."' name='".$db_name."' v-model='".$model."'>
                            <option value=''>".$placeholder."</option>
                            <option v-for='(value, key) in ".$db_name."' :value='value'>{{value}}</option>
                        </select>
                        <span class='help-block text-danger' 
                        v-if='".$error_if."'
                        v-text='".$error_end."'></span>
                    </div>
                </div>
                "
                ;

                $this->field['test_case_data'] .= "->select('".$db_name."', 1)"."\n"."\t"."\t"."\t"."\t"."\t";
        }
                

            } else if($request->input_type[$i] == "checkbox") {
                $form_fields .= 
                "
                <div class='col-sm-6'>
                    <div :class='".$first."'>
                        <p-input type='checkbox' name='".$db_name."' class='p-icon p-rotate p-bigger' color='primary' v-bind:true-value='1' v-bind:false-value='0' v-model='form.".$db_name."'>
                            <i slot='extra' class='icon mdi mdi-check'></i>
                            {{this.module.lang.".$db_name."}}
                        </p-input>
                        <span class='help-block text-danger' 
                        v-if='".$error_if."'
                        v-text='".$error_end."'></span>
                    </div>
                </div>
                ";

                $this->field['test_case_data'] .= "->check('".$db_name."')"."\n"."\t"."\t"."\t"."\t"."\t";
                
            } else if($request->input_type[$i] == "radio") {
                $form_fields .= 
                "
                <div class='col-sm-3'>
                    <label class='radio-inline radio-styled'>
                        <input type='radio' id='".$db_name."-1' name='".$db_name."' value='1' v-model='form.".$db_name."'>
                        <b> {{this.module.lang.".$db_name."}} </b>
                    </label>
                </div>
                <div class='col-sm-3'>
                    <label class='radio-inline radio-styled'>
                        <input type='radio' id='".$db_name."-2' name='".$db_name."' value='2' v-model='form.".$db_name."'>
                        <b> {{this.module.lang.".$db_name."}} </b>
                    </label>
                </div>
                ";

                $this->field['test_case_data'] .= "->radio('#".$db_name."-1', '1')"."\n"."\t"."\t"."\t"."\t"."\t";

            } else if($request->input_type[$i] == "input") {
                $form_fields .= 
                "
                <div class='col-sm-6'>
                    <div :class='".$first."'>
                        <label for='".$input_name."'> {{this.module.lang.".$db_name."}} </label>

                        <input type='text' name='".$db_name."' class='form-control' v-model='".$v_model."'>
                        <span class='help-block text-danger' 
                        v-if='".$error_if."'
                        v-text='".$error_end."'></span>
                    </div>
                </div>
                ";

                $this->field['test_case_data'] .= "->type('".$db_name."', 'Demo')"."\n"."\t"."\t"."\t"."\t"."\t";
            } else if($request->input_type[$i] == "file") {
                $form_fields .= 
                "
                <div class='col-sm-6'>
                    <div :class='".$first."'>
                        <label for='".$input_name."'> {{this.module.lang.".$db_name."}} </label>

                        <input type='file' name='".$db_name."' class='form-control' v-model='".$v_model."'>
                        <span class='help-block text-danger' 
                        v-if='".$error_if."'
                        v-text='".$error_end."'></span>
                    </div>
                </div>
                ";

                $this->field['test_case_data'] .= "->type('".$db_name."', 'Demo')"."\n"."\t"."\t"."\t"."\t"."\t";
            } else if($request->input_type[$i] == "textarea") {
                $form_fields .= 
                "
                <div class='col-sm-6'>
                    <div class='form-group'>
                        <label for='".$input_name."'> {{this.module.lang.".$db_name."}} </label>
                        
                        <textarea id='".$db_name."' name='".$db_name."' class='form-control autosize' rows='3' v-model='".$v_model."'></textarea>
                    </div>
                </div>
                ";

                $this->field['test_case_data'] .= "->type('".$db_name."', 'Text Area.Demo Test')"."\n"."\t"."\t"."\t"."\t"."\t";
            } else if($request->input_type[$i] == "date") {

            $cal = '"fa fa-calendar"';
            $fromat = '"dd/MM/yyyy"';
            $class = '"form-control"';

                $form_fields .= 
                "
                <div class='col-sm-6'>
                    <div :class='".$first."'>
                        <label> {{this.module.lang.".$db_name."}} </label>
                        <datepicker v-model='".$v_model."' :input-class='".$class."' :calendar-button-icon='".$cal."' :format='".$fromat."' id='".$db_name."' name='".$db_name."'></datepicker>
                        <span class='help-block text-danger' 
                        v-if='".$error_if."'
                        v-text='".$error_end."'></span>
                    </div>
                </div>
                ";

                $this->field['test_case_data'] .= "->value('#".$db_name."', date('m/d/Y'))"."\n"."\t"."\t"."\t"."\t"."\t";
            }

            // if(count(array_filter($request->input_type)) == ($i + 1) ){
            //     $form_fields .= "\n"."\t"."\t"."\t".'</div>'."\n";
            // }
            // if($count == 1 && count(array_filter($request->input_type)) != ($i + 1) ) {
            //     $form_fields .= "\n"."\t"."\t"."\t".'</div>'."\n";
            // }  
        
        }
        return $form_fields;
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

    //drop_search in append moduleconfig array
    public function makeDropdown($db_name){
        
        $name = "get.".$db_name;           
        return "'".$db_name."_search'"." => route('".$name."'),"."\n"."\t"."\t"."\t"."\t";
    }

    // [OptionsData]
    public function optionDataDropdown($db_name){

        return $db_name." : [],"."\n"."\t"."\t"."\t";
    }

    // [OptionsData] for empty dropdown
    public function optionDataEmptyDropdown($db_name){

        return $db_name." : Object.values(this.module.".$db_name."),"."\n"."\t"."\t"."\t";
    }

    // [EmptyDropDown]
    public function emptyDropArray($db_name, $project_path_main, $controller_name){
        $empty_data = '$data["'.$db_name.'"] = [];';
        if(file_exists($project_path_main."/app/General/ModuleConfig/".strtolower($controller_name).'.php')){

            $contents = file_get_contents($project_path_main."/app/General/ModuleConfig/".strtolower($controller_name).'.php');
            $lines = explode("\n", $contents);
            foreach($lines as $word) {
                if(strrpos($word, '$data["'.$db_name.'"]')) {
                    $empty_data = $word;
                }
            }            
        }

        return $empty_data."\n"."\t"."\t"."\t";
    }

    // [DropdownSearch]
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

    // [DropdownSearch]
    public function dropdownMountEmpty($db_name, $table_name, $dropdown_mount){
        
        if(!strpos($dropdown_mount, '$(document).ready( () => {')) {
        return "";
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

    // [FILLEBLE_LINES]
    public function prepareFillable($value) {

        return "'".strtolower($value)."',"."\n"."\t"."\t"."\t"."\t";
    }

    // [LIST_DATA_LINES]
    public function listData($request, $value, $i) {
        if($request->input_type[$i] == "dropdown" && $request->table[$i] != "") {

            return "Lang::get('".strtolower($request->table_name).'.'.strtolower($value)."') => '".strtolower($request->table[$i]).".".strtolower($request->key[$i])."'".','."\n"."\t"."\t"."\t";

        } else {

            return "Lang::get('".strtolower($request->table_name).'.'.strtolower($value)."') => '".strtolower($value)."'".','."\n"."\t"."\t"."\t";
        
        }
    }

    // [FORMELEMENT_LINES]
    public function formElement($request, $value) {

        return '"'.$value.'" => '.'"",'."\n"."\t"."\t";
    }

    // [Module_Data]
    public function moduleData($table_name) {
        
        $input_name_array = "['".$table_name."_module']";
        return '$this->data'. $input_name_array. ' = ' .'ModuleConfig::'.$table_name.'();'."\n"."\t"."\t";
    }

    // [DropdownValue]
    public function checkboxValue($db_name) {
    
        $variable = "['".$db_name."']";
        return '$input'.$variable ." = ". '$input'.$variable." ? true : false;"."\n"."\t"."\t";
    }

    // [DropdownValue] use for append db date format
    private function addParseDBDate($db_name){

        $variable = "['".$db_name."']";
        return '$input'.$variable ." = ". 'Helpers::parseDBdate($input'.$variable.");"."\n"."\t"."\t";
    }

    // [DropdownSelectedValue] use for append form date format
    private function addParseDate($db_name){

        $selected_variable = "['".$db_name."']";
        $model = 'Helpers::parseDate($model->'.$db_name.')';
        return '$formelement'.$selected_variable ." = ". "$model;"."\n"."\t"."\t";
    }

    // [LangArray]
    private function lang($lang, $request) {
        $lang .= "
        'create_title' => 'Create ".ucwords($request->main_module)."',
        'edit_title' => 'Edit ".ucwords($request->main_module)."',
        'edit_message' => '".ucwords($request->main_module)." Updated!',
        'create_message' => '".ucwords($request->main_module)." Created!',
        'delete_message' => '".ucwords($request->main_module)." Deleted!',
        'list' => '".ucwords($request->main_module)."s',
        ";
        $string = "<?php"."\n"."\n"."return ["."\n"."\t"."\t".$lang."\n".'];';

        return $string;
    }
}
