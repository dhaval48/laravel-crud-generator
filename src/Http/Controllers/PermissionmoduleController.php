<?php

namespace Ongoingcloud\Laravelcrud\Http\Controllers;

use Ongoingcloud\Laravelcrud\Helpers;
use Ongoingcloud\Laravelcrud\General\ModuleConfig;
use Ongoingcloud\Laravelcrud\General\HandlePermission;
use App\Http\Controllers\Controller;
use Ongoingcloud\Laravelcrud\Models\Permissionmodule as Module;
use Ongoingcloud\Laravelcrud\Models\Formmodule;
use Auth;
use Illuminate\Http\Request;
use PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

use Ongoingcloud\Laravelcrud\Http\Requests\Permissionmodule\StorePermissionmoduleRequest;
use Ongoingcloud\Laravelcrud\Http\Requests\Permissionmodule\UpdatePermissionmoduleRequest;
use Ongoingcloud\Laravelcrud\Http\Requests\Permissionmodule\DeletePermissionmoduleRequest;
use Ongoingcloud\Laravelcrud\Http\Requests\Permissionmodule\ListPermissionmoduleRequest;
use Ongoingcloud\Laravelcrud\Http\Requests\Permissionmodule\OnlyPermissionmoduleRequest;
use Lang;

class PermissionmoduleController extends Controller {

    public $data = [];        
    
    public $form_view = 'backend.modules.permissionmodule';
    public $form_export = 'backend.modules.permissionmodule-table';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function default() {
        
        $this->data = ModuleConfig::permission_modules();
         // [Module_Data]
    }

    public function index(ListPermissionmoduleRequest $request) { 
        $this->default(); 
        $model = new Module;
        $this->data['permissions'] = HandlePermission::getPermissionsVue($this->data['dir']);

        $this->data['lists'] = Module::latest()->paginate(25);
        $only = new OnlyPermissionmoduleRequest();
        if($only->authorize()){
            $this->data['lists'] = Module::latest()->where('created_by', \Auth::user()->id)->paginate(25);
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
                                });
        } else {
            $lists = Module::latest();
        }

        $only = new OnlyPermissionmoduleRequest();
        if($only->authorize()){
            $lists = $lists->where('created_by', \Auth::user()->id);
        }

        $this->data['list_data'] = $model->list_data();

        if($request->pdf) {
            $this->data['lists'] = $lists->get();

            $data = $this->data;
            
            $pdf = PDF::loadView($this->form_export, compact('data'));        
            return $pdf->download('Permissionmodule.pdf');
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
            header('Content-Disposition: attachment; filename="Permissionmodule.csv"');
            return $writer->save("php://output");
        }

        if($from_delete) {
            return $this->data;
        }

        $this->data['lists'] = $lists->paginate(25);
        return Helpers::successResponse(Lang::get('label.notification.success_message'),$this->data);
    }

    public function create(StorePermissionmoduleRequest $request) {
        $this->default(); 
        
        return view($this->form_view, ['data'=>$this->data]);
    }

    public function store(StorePermissionmoduleRequest $request) {
        $this->default(); 
        $this->validate($request, [
                "name" => "required",
			
            ]
        );  

        if(isset($request->id)) {
            if(Helpers::isDuplicate('permission_modules','name',$request->name, $request->id)) {
                return Helpers::errorResponse('That name already exists. Please choose a different name');
            }
        } else {
            if(Helpers::isUnique('permission_modules','name',$request->name)) {
                return Helpers::errorResponse('That name already exists. Please choose a different name');
            }
        }
         // [GridValidation]
        $input = $request->all();
        //[DropdownValue]
        
        \DB::beginTransaction();   
        try {
            if(isset($request->id)) {
                $model = Module::find($request->id);
                 // [GridActivity]
                 // [GridDelete]
                $model->update($input);
            } else {
                $input["created_by"] = \Auth::user()->id;
                $model = Module::Create($input);
            }
             // [GridSave]
            
        } catch (\Exception $e) {
            dd($e);
            \DB::rollback();
            return Helpers::errorResponse();
        }
        \DB::commit();

        $message = isset($request->id) ? Lang::get('permission_modules.edit_message') : Lang::get('permission_modules.create_message');
        return Helpers::successResponse($message,$model);
    }

    public function edit($id, UpdatePermissionmoduleRequest $request) {
        $this->default(); 
        $this->data['id'] = $id;
        $model = Module::findorfail($id);
        $formelement = $model->getAttributes();
        $formelement['_token'] = csrf_token();
        
        
        // [DropdownSelectedValue]

         // [GridEdit]
        $this->data['fillable'] = $formelement;

        $this->data['permissions'] = HandlePermission::getPermissionsVue($this->data['dir']);

        $only = new OnlyPermissionmoduleRequest();
        if(!$only->authorize() || $model->created_by == \Auth::user()->id) {
            return view($this->form_view, ['data'=>$this->data]);
        } else {
            return "Unauthorized!";
        }
    }

    public function destroy(DeletePermissionmoduleRequest $request){
        $this->default(); 
        $this->data['permissions'] = HandlePermission::getPermissionsVue($this->data['dir']);

        if($request->id == 1 || $request->id == 2) {
            return Helpers::errorResponse('You can not delete because it is default module!');
        }

        $model = Module::findorfail($request->id);

        $module = Formmodule::where('parent_module',$model->name)->first();

        if($module) {
            return Helpers::errorResponse('You can not delete because it is used in form!');
        }

        \DB::beginTransaction();
        try {
             // [GridDelete]
            $model->delete();
        } catch (\Exception $e) {
            \DB::rollback();                        
            return Helpers::errorResponse('Error while deleting permissionmodule. Try again latter');
        }
        \DB::commit();

        $model = new Module;
        $this->data['lists'] = Module::latest()->paginate(25);
        $only = new OnlyPermissionmoduleRequest();
        if($only->authorize()){
            $this->data['lists'] = Module::latest()->where('created_by', \Auth::user()->id)->paginate(25);
        }

        $this->data['list_data'] = $model->list_data();
        return Helpers::successResponse(Lang::get('permission_modules.delete_message'),$this->data);
    }
}
