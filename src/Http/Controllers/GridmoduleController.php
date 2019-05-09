<?php

namespace Ongoingcloud\Laravelcrud\Http\Controllers;

use Ongoingcloud\Laravelcrud\Helpers;
use Ongoingcloud\Laravelcrud\General\ModuleConfig;
use Ongoingcloud\Laravelcrud\General\HandlePermission;
use Ongoingcloud\Laravelcrud\General\Grid;
use Ongoingcloud\Laravelcrud\General\RollbackGrid;
use Ongoingcloud\Laravelcrud\General\Helper;
use Ongoingcloud\Laravelcrud\General\Rollback;
use App\Http\Controllers\Controller;
use Ongoingcloud\Laravelcrud\Models\Formmodule as Module;
use Auth;
use Illuminate\Http\Request;
use PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

use Ongoingcloud\Laravelcrud\Http\Requests\Formmodule\StoreFormmoduleRequest;
use Ongoingcloud\Laravelcrud\Http\Requests\Formmodule\UpdateFormmoduleRequest;
use Ongoingcloud\Laravelcrud\Http\Requests\Formmodule\DeleteFormmoduleRequest;
use Ongoingcloud\Laravelcrud\Http\Requests\Formmodule\ListFormmoduleRequest;
use Ongoingcloud\Laravelcrud\Http\Requests\Formmodule\OnlyFormmoduleRequest;
use Lang;

class GridmoduleController extends Controller {

    public $data = [];        
    
    public $form_view = 'backend.modules.gridmodule';
    public $form_export = 'backend.modules.gridmodule-table';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function default() {
        
        $this->data = ModuleConfig::grid_modules();
         // $this->data['form_modules_module'] = ModuleConfig::form_modules();
        $this->data['permission_modules_module'] = ModuleConfig::permission_modules();
        // [Module_Data]
    }

    public function index(ListFormmoduleRequest $request) { 
        $this->default(); 
        $model = new Module;
        $this->data['permissions'] = HandlePermission::getPermissionsVue($this->data['dir']);

        $this->data['lists'] = Module::latest()->wherenull('deleted_at')->whereNotNull('parent_form')->paginate(25);
        $only = new OnlyFormmoduleRequest();
        if($only->authorize()){
            $this->data['lists'] = Module::latest()->wherenull('deleted_at')->whereNotNull('parent_form')->where('created_by', \Auth::user()->id)->paginate(25);
        }

        $this->data['list_data'] = $model->grid_data();
        $this->data['fillable'] = Helpers::formatDeleteFillable();
        return view($this->form_view, ['data'=>$this->data]);
    }

    public function Paginate($from_delete = false ,Request $request) {
        $this->default(); 

        $this->data['permissions'] = HandlePermission::getPermissionsVue($this->data['dir']);
        
        $model = new Module;
        $searchelements = $model->searchelements;


        if(isset($request->q) && !empty($request->q)) {

            $lists = Module::latest()->where(function($query) use ($searchelements, $request) {
                                    foreach ($searchelements as $key => $value) {

                                        $query = $query->orwhere($value,'like','%'.$request->q.'%');
                                    }
                                })->wherenull('deleted_at')->whereNotNull('parent_form');
        } else {
            $lists = Module::latest()->wherenull('deleted_at')->whereNotNull('parent_form');
        }

        $only = new OnlyFormmoduleRequest();
        if($only->authorize()){
            $lists = $lists->where('created_by', \Auth::user()->id);
        }

        $this->data['list_data'] = $model->grid_data();

        if($request->pdf) {
            $this->data['lists'] = $lists->get();

            $data = $this->data;
            
            $pdf = PDF::loadView($this->form_export, compact('data'));        
            return $pdf->download('Formmodule.pdf');
        }

        if($request->csv) {
            $this->data['lists'] = $lists->get();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            $i = 0;
            
            foreach ($this->data['list_data'] as $field => $value) {
                $sheet->setCellValue(range('A', 'Z')[$i]."1", $field);
                $sheet->getStyle(range('A', 'Z')[$i])->getFont()->setSize(10); 
                $sheet->getStyle(range('A', 'Z')[$i]."1")->getFont()->setBold( true );
                $i++;
            }

            $rows = 2;
            $j = 0;
            foreach($this->data['lists'] as $value){

                foreach ($this->data['list_data'] as $list_data) {
                    $list = Helpers::getRelation($value, $list_data);
                    $sheet->setCellValue(range('A', 'Z')[$j].$rows, $list);
                    $sheet->getColumnDimension(range('A', 'Z')[$j])
                        ->setAutoSize(true);
                    $sheet->getStyle(range('A', 'Z')[$j])->getFont()->setSize(10); 
                    $j++;
                }
                $j = 0;
                $rows++;
            }
            $writer = new Csv($spreadsheet);
            header("Content-Type: application/vnd.ms-excel");
            header('Content-Disposition: attachment; filename="Formmodule.csv"');
            return $writer->save("php://output");
        }

        if($from_delete) {
            return $this->data;
        }

        $this->data['lists'] = $lists->paginate(25);
        return Helpers::successResponse(Lang::get('label.notification.success_message'),$this->data);
    }

    public function create(StoreFormmoduleRequest $request) {
        $this->default(); 
        
        return view($this->form_view, ['data'=>$this->data]);
    }

    public function store(StoreFormmoduleRequest $request) {
        $this->default(); 
        $this->validate($request, [
                'parent_form' => 'required',
                // 'parent_module' => 'required',
                'main_module' => 'required',
                'table_name' => 'required',
            ]
        );   

        $model = Module::find($request->id);
        if(Helpers::existTabale($request, $model)) {
            return Helpers::errorResponse("This table is already exist in your database");
        }

        if(Helpers::ifExistFile($request, $model)) {
            return Helpers::errorResponse("This form is already exist in your project");
        }

        $array = [];
        $rows = count($request->type);
        for ($i = 0; $i < $rows; $i++) {
            if($request->name[$i] == 'id') {
                return Helpers::errorResponse("id is default field in table. please, remove id from table fields");
            }
            $array["name"] = $request->name[$i];
            $array["type"] = $request->type[$i];
            if(isset($request->module_input_id[$i])) {
                $module_table_data = \DB::table('module_tables')->where('id', $request->module_input_id[$i])->first();
                
                $table_data = \DB::table($model->table_name)->get();

                if($table_data){
                    if($module_table_data->type != $request->type[$i]){
                        $name = $module_table_data->name;
                        foreach ($table_data as $key => $value) {
                            if(!is_numeric($value->$name) || !is_int($value->$name)) {
                                return Helpers::errorResponse("Please delete the existing data from the ".$request->table_name." table");
                            }
                        }
                    }
                }
            }
            if(count(array_filter($array)) != 0) {
                foreach ($array as $key => $value) {
                    if($value == ""){
                        return Helpers::errorResponse(ucfirst($key)." Field is required");
                    }
                }
            } else {
                if($rows == 1) {
                    return Helpers::errorResponse("Add table field. Atleast one row is required");
                }
            }
        }

        $array = [];
        $rows = count($request->input_type);
        for ($i = 0; $i < $rows; $i++) {
            if($request->visible[$i]) {
                $array["input name"] = $request->input_name[$i];
                $array["input type"] = $request->input_type[$i];
                if($request->table[$i] != "") {
                    $array["value"] = $request->value[$i];
                    $array["label"] = $request->key[$i];
                }

                if($request->key[$i] != "") {
                    $array["value"] = $request->value[$i];
                    $array["table"] = $request->table[$i];
                }

                if($request->value[$i] != "") {
                    $array["label"] = $request->key[$i];
                    $array["table"] = $request->table[$i];
                }

                if(count(array_filter($array)) != 0) {
                    foreach ($array as $key => $value) {
                        if($value == ""){
                            return Helpers::errorResponse(ucfirst($key)." Field is required");
                        }
                    }
                } else {
                    if($rows == 1) {
                        return Helpers::errorResponse("Add input fields. Atleast one row is required");
                    }
                }
            }
        }
        // [GridValidation]
        $input = $request->all();
        //[DropdownValue]
        $module = "";
        if($model) {
            $parent_module = Module::where('main_module', $model->parent_form)->first();   
        }
        
        \DB::beginTransaction();   
        try {
            if(isset($request->id)) {
                
                $module = $this->getExistData($parent_module);

                $rollback = new Rollback;

                $rollback->deleteFiles($module);

                $helper = new Helper;
                
                $helper->makeFiles($module, false, $module);

                $module_field = $this->parentModuleField($parent_module);

                $module_table = "";
                $module_input = "";
                foreach ($parent_module->module_tables as $key => $value) {
                    $module_table .= $this->parentTableField($value);
                }

                foreach ($parent_module->module_inputs as $key => $value) {
                    $module_input .= $this->parentInputField($value);
                }

                if(env('APP_ENV') == 'local'){
                    $this->makeMigration($parent_module, $module_field, $module_table, $module_input);
                }

                $module = $this->getExistData($model);

                $rollback_grid = new RollbackGrid;

                $rollback_grid->deleteFiles($module);

                $grid = new Grid;

                $grid->makeFiles($request, false, $module);

                // [GridActivity]
                $model->module_tables()->where("formmodule_id", $request->id)->delete();
                $model->module_inputs()->where("formmodule_id", $request->id)->delete();
                // [GridDelete]
                $model->update($input);
            } else {
                $input["created_by"] = \Auth::user()->id;
                $model = Module::Create($input);

                $grid = new Grid;

                $grid->makeFiles($request, false, $module);
            }

            $module_field = $this->makeModuleField($request);

            $module_table = "";
            $module_input = "";
            for($i=0; $i < count(array_filter($request->type)); $i++) {
                $module_table .= $this->makeTableField($request, $i);

                $model->module_tables()->create([
                            "formmodule_id" => $model->id,
                            'name' => $request->name[$i],
                            'type' => $request->type[$i],
                            'validation' => $request->validation[$i],
                            'default' => $request->default[$i],
                ]);
            }
            for($i=0; $i < count(array_filter($request->db_name)); $i++) {
                $module_input .= $this->makeInputField($request, $i);

                $model->module_inputs()->create([
                            "formmodule_id" => $model->id,
                            'visible' => $request->visible[$i],
                            'input_name' => $request->input_name[$i],
                            'db_name' => $request->db_name[$i],
                            'input_type' => $request->input_type[$i],
                            'key' => $request->key[$i],
                            'value' => $request->value[$i],
                            'table' => $request->table[$i],
                ]);
            }

                // [GridSave]
            
            if(env('APP_ENV') == 'local'){
                $this->makeMigration($request, $module_field, $module_table, $module_input);
            }

        } catch (\Exception $e) {
            if(!isset($request->id)) {
                
                $rollback_grid = new RollbackGrid;

                $rollback_grid->deleteFiles($request, false, true);
            }
            \DB::rollback();
            return Helpers::errorResponse();
        }
        \DB::commit();

        \Artisan::call('migrate');

        $message = isset($request->id) ? Lang::get('form_modules.edit_message') : Lang::get('form_modules.create_message');
        return Helpers::successResponse($message,$model);
    }

    public function edit($id, UpdateFormmoduleRequest $request) {
        $this->default(); 
        $this->data['id'] = $id;
        $model = Module::findorfail($id);
        $formelement = $model->getAttributes();
        $formelement['_token'] = csrf_token();
        
        // [DropdownSelectedValue]

         if(count($model->module_tables) > 0 ) {
            $this->data["module_tables_row"] = [];
            $this->data["module_tablesrow_count"] = count($model->module_tables) - 1;
            foreach ($model->module_tables as $key => $value) {
                $this->data["module_tables_row"][] = $key;
                $formelement['name'][] = $value->name;
                $formelement["module_input_id"][] = $value->id;

                $formelement["db_name"][] = $value->name;
                
                $formelement['type'][] = $value->type;
                $formelement['validation'][] = $value->validation;
                $formelement['default'][] = $value->default;
                
            }
        } else {
            $formelement['name'][] = "";
            $formelement["db_name"][] = "";
            $formelement['type'][] = "";
            $formelement['validation'][] = "";
            $formelement['default'][] = "";
            
        }
        if(count($model->module_inputs) > 0 ) {
            $this->data["module_inputs_row"] = [];
            $this->data["module_inputsrow_count"] = count($model->module_inputs) - 1;
            foreach ($model->module_inputs as $key => $value) {
                $this->data["module_inputs_row"][] = $key;
                $formelement["visible"][] = $value->visible;
                $formelement['input_name'][] = $value->input_name;

                $formelement['input_type'][] = $value->input_type ? $value->input_type : '';
                $formelement['key'][] = $value->key ? $value->key : '';
                $formelement['value'][] = $value->value ? $value->value : '';
                $formelement['table'][] = $value->table ? $value->table : '';
                
            }
        } else {
            $formelement["visible"][] = true;
            $formelement['input_name'][] = "";
            $formelement['input_type'][] = "";
            $formelement['key'][] = "";
            $formelement['value'][] = "";
            $formelement['table'][] = "";
            
        }
        // [GridEdit]
        $this->data['fillable'] = $formelement;

        $this->data['permissions'] = HandlePermission::getPermissionsVue($this->data['dir']);

        $only = new OnlyFormmoduleRequest();
        if(!$only->authorize() || $model->created_by == \Auth::user()->id) {
            return view($this->form_view, ['data'=>$this->data]);
        } else {
            return "Unauthorized!";
        }
    }

    public function destroy(DeleteFormmoduleRequest $request){
        $this->default(); 
        $this->data['permissions'] = HandlePermission::getPermissionsVue($this->data['dir']);
        
        \DB::beginTransaction();
        try {
            $model = Module::findorfail($request->id);

            $parent_module = Module::where('main_module', $model->parent_form)->first();

            $module = $this->getExistData($parent_module);

            $rollback = new Rollback;

            $rollback->deleteFiles($module);

            $helper = new Helper;
            
            $helper->makeFiles($module, false, $module);

            $module_field = $this->parentModuleField($parent_module);

            $module_table = "";
            $module_input = "";
            foreach ($parent_module->module_tables as $key => $value) {
                $module_table .= $this->parentTableField($value);
            }

            foreach ($parent_module->module_inputs as $key => $value) {
                $module_input .= $this->parentInputField($value);
            }

            if(env('APP_ENV') == 'local'){
                $this->makeMigration($parent_module, $module_field, $module_table, $module_input);
            }

            $module = $this->getExistData($model);

            $rollback_grid = new RollbackGrid;

            $rollback_grid->deleteFiles($module, false, true);

            $model->module_tables()->where("formmodule_id", $request->id)->delete();
            $model->module_inputs()->where("formmodule_id", $request->id)->delete();
                // [GridDelete]
            $model->delete();
        } catch (\Exception $e) {
            \DB::rollback();                        
            return Helpers::errorResponse('Error while deleting formmodule. Try again latter');
        }
        \DB::commit();

        $model = new Module;
        $this->data['lists'] = Module::latest()->wherenull('deleted_at')->whereNotNull('parent_form')->paginate(25);
        $only = new OnlyFormmoduleRequest();
        if($only->authorize()){
            $this->data['lists'] = Module::latest()->wherenull('deleted_at')->whereNotNull('parent_form')->where('created_by', \Auth::user()->id)->paginate(25);
        }
        
        $this->data['list_data'] = $model->grid_data();
        return Helpers::successResponse(Lang::get('form_modules.delete_message'),$this->data);
    }

    public function makeModuleField($request) {
        return  "
                 ".$this->getValue('parent_module', $request->parent_module)."
                 'main_module' => '".$request->main_module."',
                 'table_name' => '".$request->table_name."',
                 'parent_form' => '".$request->parent_form."',
                ";
    }

    public function makeTableField($request, $i) {
        return "
                    [
                        'formmodule_id' => ".'$module_id'.",
                        'name' => '".$request->name[$i]."',
                        'type' => '".$request->type[$i]."',
                        ".$this->getValue('validation', $request->validation[$i])."
                        ".$this->getValue('default', $request->default[$i])."
                    ],
                    ";
    }

    public function makeInputField($request, $i) {
        $visible = $request->visible[$i] ? 1 : 0;
        return "
                    [
                        'formmodule_id' => ".'$module_id'.",
                        'visible' => ".$visible.",
                        'db_name' => '".$request->db_name[$i]."',
                        'input_name' => '".$request->input_name[$i]."',
                        'input_type' => '".$request->input_type[$i]."',
                        ".$this->getValue('key', $request->key[$i])."
                        ".$this->getValue('value', $request->value[$i])."
                        ".$this->getValue('table', $request->table[$i])."
                    ],
                    ";
    }


    public function parentModuleField($request) {
        return  "
                 'parent_module' => '".$request->parent_module."',
                 'main_module' => '".$request->main_module."',
                 'table_name' => '".$request->table_name."',
                ";
    }

    public function parentTableField($request) {
        return "
                    [
                        'formmodule_id' => ".'$module_id'.",
                        'name' => '".$request->name."',
                        'type' => '".$request->type."',
                        ".$this->getValue('validation', $request->validation)."
                        ".$this->getValue('default', $request->default)."
                    ],
                    ";
    }

    public function parentInputField($request) {
        return "
                    [
                        'formmodule_id' => ".'$module_id'.",
                        'visible' => ".$request->visible.",
                        'db_name' => '".$request->db_name."',
                        'input_name' => '".$request->input_name."',
                        'input_type' => '".$request->input_type."',
                        ".$this->getValue('key', $request->key)."
                        ".$this->getValue('value', $request->value)."
                        ".$this->getValue('table', $request->table)."
                    ],
                    ";
    }

    private function getValue($field, $value) {
        if(empty($value)) {
            return "'".$field."' => null,";
        }
        return "'".$field."' => "."'".$value."',";
    }

    public function makeMigration($request, $module_field, $module_table, $module_input) {
        $file_name = 'auto_'.$request->table_name."_table";
        $existing_path = '';
        $migration_path = base_path()."/database/migrations";
        $migration_files = scandir($migration_path);
        foreach ($migration_files as $value) {
            if(strrpos($value, $file_name)) {
                $existing_path = $value;
            }

        }

        $class_name = ucfirst($request->table_name);
        $classArr = explode('_',$class_name);
        if(count($classArr) > 0) {
            $class_name = 'Auto';
            foreach($classArr as $class) {
                $class_name .= ucfirst($class);
            }
        }

        $route = base_path().'/vendor/ongoingcloud/laravelcrud/Vuesample/datamigration.php';
        $content = file_get_contents($route);

        $content = preg_replace('/\\['.preg_quote('CLASS_MODULE','/').'\\]/',$class_name,$content); 

        $content = preg_replace('/\\['.preg_quote('EXIST_MODULE','/').'\\]/',$request->main_module,$content);
        
        $content = preg_replace('/\\['.preg_quote('DATA_MODULE','/').'\\]/',$module_field,$content);

        $content = preg_replace('/\\['.preg_quote('DATA_MODULE_TABLE','/').'\\]/',$module_table,$content);

        $content = preg_replace('/\\['.preg_quote('DATA_MODULE_INPUT','/').'\\]/',$module_input,$content);

        if($existing_path != '') {
            file_put_contents(base_path().'/database/migrations/'.$existing_path, $content);
        } else {
            $path = exec("php ".base_path()."/artisan make:migration create_auto_".$request->table_name."_table");

            $migration_file = str_replace('Created Migration: ', '',$path);
            $migration_file = $migration_file.'.php';
            file_put_contents(base_path().'/database/migrations/'.$migration_file, $content);
        }
    }

    public function getExistData($module) {
        
        $table = $module->module_tables()->get();
        $input = $module->module_inputs()->get();

        $module = $module->toArray();

        foreach ($table as $key => $value) {

            $module['module_input_id'][] = $value->id;
            $module['name'][] = $value->name;
            $module['type'][] = $value->type;

            $module['validation'][] = $value->validation;

            $module['default'][] = $value->default;

        }

        foreach ($input as $key => $value) {

            $module['visible'][] = $value->visible;
            $module['input_name'][] = $value->input_name;
            $module['db_name'][] = $value->db_name;
            $module['input_type'][] = $value->input_type;

            $module['key'][] = $value->key;

            $module['value'][] = $value->value;
            $module['table'][] = $value->table;
            
        }
        $module = (object) $module;
        return $module;
    }


    public function make_production($id) {
        $model = Module::find($request->id);
        
        $parent_module = Module::where('main_module', $model->parent_form)->first();

        $module = $this->getExistData($parent_module);

        $rollback = new Rollback;

        $rollback->deleteFiles($module);

        $helper = new Helper;
        
        $helper->makeFiles($module);

        // $module_field = $this->parentModuleField($parent_module);

        // $module_table = "";
        // $module_input = "";
        // foreach ($parent_module->module_tables as $key => $value) {
        //     $module_table .= $this->parentTableField($value);
        // }

        // foreach ($parent_module->module_inputs as $key => $value) {
        //     $module_input .= $this->parentInputField($value);
        // }

        // if(env('APP_ENV') == 'local'){
        //     $this->makeMigration($parent_module, $module_field, $module_table, $module_input);
        // }

        $module = $this->getExistData($model);

        $rollback_grid = new RollbackGrid;

        $rollback_grid->deleteFiles($module);

        $grid = new Grid;
        
        $grid->makeFiles($module);
        
        return redirect()->back();

    }
}
