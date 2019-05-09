<?php

namespace Ongoingcloud\Laravelcrud\Http\Controllers;

use Ongoingcloud\Laravelcrud\Helpers;
use Ongoingcloud\Laravelcrud\General\ModuleConfig;
use Ongoingcloud\Laravelcrud\General\HandlePermission;
use Ongoingcloud\Laravelcrud\General\ApiHelper;
use Ongoingcloud\Laravelcrud\General\ApiRollback;
use App\Http\Controllers\Controller;
use Ongoingcloud\Laravelcrud\Models\Apimodule as Module;
use Auth;
use Illuminate\Http\Request;
use PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

use Ongoingcloud\Laravelcrud\Http\Requests\Apimodule\StoreApimoduleRequest;
use Ongoingcloud\Laravelcrud\Http\Requests\Apimodule\UpdateApimoduleRequest;
use Ongoingcloud\Laravelcrud\Http\Requests\Apimodule\DeleteApimoduleRequest;
use Ongoingcloud\Laravelcrud\Http\Requests\Apimodule\ListApimoduleRequest;
use Ongoingcloud\Laravelcrud\Http\Requests\Apimodule\OnlyApimoduleRequest;
use Lang;

class ApimoduleController extends Controller {

    public $data = [];        
    
    public $form_view = 'backend.modules.apimodule';
    public $form_export = 'backend.modules.apimodule-table';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function default() {
        
        $this->data = ModuleConfig::api_modules();
        // $this->data['api_modules_module'] = ModuleConfig::api_modules();
        $this->data['permission_modules_module'] = ModuleConfig::permission_modules();
        // [Module_Data]
    }

    public function index(ListApimoduleRequest $request) {
        $this->default(); 
        $model = new Module;
        $this->data['permissions'] = HandlePermission::getPermissionsVue($this->data['dir']);

        $this->data['lists'] = Module::latest()->wherenull('deleted_at')->wherenull('parent_form')->paginate(25);
        $only = new OnlyApimoduleRequest();
        if($only->authorize()){
            $this->data['lists'] =  Module::latest()->wherenull('deleted_at')->wherenull('parent_form')->where('created_by', \Auth::user()->id)->paginate(25);
        }

        $this->data['list_data'] = $model->list_data();
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
                                })->wherenull('deleted_at')->wherenull('parent_form');;
        } else {
            $lists = Module::latest()->wherenull('deleted_at')->wherenull('parent_form');
        }

        $only = new OnlyApimoduleRequest();
        if($only->authorize()){
            $lists = $lists->where('created_by', \Auth::user()->id);
        }

        $this->data['list_data'] = $model->list_data()->wherenull('deleted_at')->wherenull('parent_form');

        if($request->pdf) {
            $this->data['lists'] = $lists->get();

            $data = $this->data;
            
            $pdf = PDF::loadView($this->form_export, compact('data'));        
            return $pdf->download('Apimodule.pdf');
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
            header('Content-Disposition: attachment; filename="Apimodule.csv"');
            return $writer->save("php://output");
        }

        if($from_delete) {
            return $this->data;
        }

        $this->data['lists'] = $lists->paginate(25);
        return Helpers::successResponse(Lang::get('label.notification.success_message'),$this->data);
    }

    public function create(StoreApimoduleRequest $request) {
        $this->default(); 
        
        return view($this->form_view, ['data'=>$this->data]);
    }

    public function store(StoreApimoduleRequest $request) {
        $this->default(); 
        // dd($request->all());
        $this->validate($request, [
                // 'parent_module' => 'required',
                'main_module' => 'required',
                'table_name' => 'required',
            ]
        );

        $model = Module::find($request->id);

        if($request->is_model) {
            if(Helpers::existTabale($request, $model)) {
                return Helpers::errorResponse("This table is already exist in your database");
            }

            if(Helpers::ifExistFile($request, $model)) {
                return Helpers::errorResponse("This form is already exist in your project");
            }
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
                $module_table_data = \DB::table('api_tables')->where('id', $request->module_input_id[$i])->first();
                
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
                    return Helpers::errorResponse("Add table fields. Atleast one row is required");
                }
            }
        }
        // [GridValidation]
        $input = $request->all();
        //[DropdownValue]
        $module = "";
        \DB::beginTransaction();   
        try {
            if(isset($request->id)) {
                $module = $this->getExistData($request->id);

                $rollback = new ApiRollback;

                $rollback->deleteFiles($module);

                $helper = new ApiHelper;
            
                $helper->makeFiles($request, false, $module);

                $model = Module::find($request->id);
                
                // [GridActivity]
                 $model->api_tables()->where("apimodule_id", $request->id)->delete();
                // [GridDelete]
                $model->update($input);
            } else {
                $input["created_by"] = \Auth::user()->id;
                $model = Module::Create($input);

                $helper = new ApiHelper;
            
                $helper->makeFiles($request, false, $module);
            }

            $module_field = $this->makeModuleField($request);

            $module_table = "";
            for($i=0; $i < count(array_filter($request->type)); $i++) {
                $module_table .= $this->makeTableField($request, $i);

                $model->api_tables()->create([
                            "apimodule_id" => $model->id,
                            'name' => $request->name[$i],
                            'type' => $request->type[$i],
                            'validation' => $request->validation[$i],
                            'default' => $request->default[$i],
                            
                ]);
            }
                // [GridSave]

            if(env('APP_ENV') == 'local'){
                $this->makeMigration($request, $module_field, $module_table);
            }
            
            \Artisan::call('migrate');

        } catch (\Exception $e) {
            if(!isset($request->id)) {
                $rollback = new ApiRollback;
                
                $rollback->deleteFiles($request, false, true);
            }
            \DB::rollback();
            return Helpers::errorResponse();
        }
        \DB::commit();

        $message = isset($request->id) ? Lang::get('api_modules.edit_message') : Lang::get('api_modules.create_message');
        return Helpers::successResponse($message,$model);
    }

    public function edit($id, UpdateApimoduleRequest $request) {
        $this->default(); 

        $this->data['id'] = $id;
        $model = Module::findorfail($id);
        $formelement = $model->getAttributes();
        $formelement['_token'] = csrf_token();
  
        // [DropdownSelectedValue]

         if(count($model->api_tables) > 0 ) {
            $this->data["api_tables_row"] = [];
            $this->data["api_tablesrow_count"] = count($model->api_tables) - 1;
            foreach ($model->api_tables as $key => $value) {
                $this->data["api_tables_row"][] = $key;
                $formelement['name'][] = $value->name;
                $formelement["module_input_id"][] = $value->id;
                $formelement['type'][] = $value->type;
                $formelement['validation'][] = $value->validation;
                $formelement['default'][] = $value->default;
                
            }
        } else {
            $formelement['name'][] = "";
            $formelement['type'][] = "";
            $formelement['validation'][] = "";
            $formelement['default'][] = "";
            
        }
        // [GridEdit]
        $this->data['fillable'] = $formelement;

        $this->data['permissions'] = HandlePermission::getPermissionsVue($this->data['dir']);

        $only = new OnlyApimoduleRequest();
        if(!$only->authorize() || $model->created_by == \Auth::user()->id) {
            return view($this->form_view, ['data'=>$this->data]);
        } else {
            return "Unauthorized!";
        }        
    }

    public function destroy(DeleteApimoduleRequest $request){
        $this->default(); 
        
        $this->data['permissions'] = HandlePermission::getPermissionsVue($this->data['dir']);
        
        \DB::beginTransaction();
        try {
            $module = $this->getExistData($request->id);

            $rollback = new ApiRollback;
            
            $rollback->deleteFiles($module, false, true);

            $model = Module::findorfail($request->id);
            
            $model->api_tables()->where("apimodule_id", $request->id)->delete();
                // [GridDelete]
            $model->delete();
        } catch (\Exception $e) {
            \DB::rollback();                        
            return Helpers::errorResponse('Error while deleting apimodule. Try again latter');
        }
        \DB::commit();

        $model = new Module;
        $this->data['lists'] = Module::latest()->wherenull('deleted_at')->wherenull('parent_form')->paginate(25);
        $only = new OnlyApimoduleRequest();
        if($only->authorize()){
            $this->data['lists'] = Module::latest()->wherenull('deleted_at')->wherenull('parent_form')->where('created_by', \Auth::user()->id)->paginate(25);
        }

        $this->data['list_data'] = $model->list_data();
        return Helpers::successResponse(Lang::get('api_modules.delete_message'),$this->data);
    }

    public function makeModuleField($request) {
        $is_model = $request->is_model ? 1 : 0;
        $is_public = $request->is_public ? 1 : 0;
        return  "
                 ".$this->getValue('parent_module', $request->parent_module)."
                 'main_module' => '".$request->main_module."',
                 'table_name' => '".$request->table_name."',
                 'is_model' => ".$is_model.",
                 'is_public' => ".$is_public.",
                ";
    }

    public function makeTableField($request, $i) {
        return "
                    [
                        'apimodule_id' => ".'$module_id'.",
                        'name' => '".$request->name[$i]."',
                        'type' => '".$request->type[$i]."',
                        ".$this->getValue('validation', $request->validation[$i])."
                        ".$this->getValue('default', $request->default[$i])."
                    ],
                    ";
    }

    private function getValue($field, $value) {
        if(empty($value)) {
            return "'".$field."' => null,";
        }
        return "'".$field."' => "."'".$value."',";
    }

    public function makeMigration($request, $module_field, $module_table) {
        $file_name = 'auto_api_'.$request->table_name."_table";
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
            $class_name = 'AutoApi';
            foreach($classArr as $class) {
                $class_name .= ucfirst($class);
            }
        }

        $route = base_path().'/vendor/ongoingcloud/laravelcrud/Apisample/datamigration.php';
        $content = file_get_contents($route);

        $content = preg_replace('/\\['.preg_quote('CLASS_MODULE','/').'\\]/',$class_name,$content); 

        $content = preg_replace('/\\['.preg_quote('EXIST_MODULE','/').'\\]/',$request->main_module,$content);
        
        $content = preg_replace('/\\['.preg_quote('DATA_MODULE','/').'\\]/',$module_field,$content);

        $content = preg_replace('/\\['.preg_quote('DATA_MODULE_TABLE','/').'\\]/',$module_table,$content);

        if($existing_path != '') {
            file_put_contents(base_path().'/database/migrations/'.$existing_path, $content);
        } else {
            $path = exec("php ".base_path()."/artisan make:migration create_auto_api_".$request->table_name."_table");

            $migration_file = str_replace('Created Migration: ', '',$path);
            $migration_file = $migration_file.'.php';
            file_put_contents(base_path().'/database/migrations/'.$migration_file, $content);
        }
    }

    public function getExistData($id) {
        $module = Module::findorfail($id); 
        
        $table = $module->api_tables()->get();
        
        $module = $module->toArray();

        foreach ($table as $key => $value) {

            $module['module_input_id'][] = $value->id;
            $module['name'][] = $value->name;
            $module['type'][] = $value->type;

            $module['validation'][] = $value->validation;

            $module['default'][] = $value->default;

        }
        $module = (object) $module;

        return $module;
    }

    public function make_production($id) {
       
        $module = $this->getExistData($id);

        $rollback = new ApiRollback;

        $rollback->deleteFiles($module);

        $helper = new ApiHelper;
        
        $helper->makeFiles($module);
        
        return redirect()->back();

    }
}
