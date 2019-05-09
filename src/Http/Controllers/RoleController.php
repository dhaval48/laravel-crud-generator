<?php

namespace Ongoingcloud\Laravelcrud\Http\Controllers;

use Ongoingcloud\Laravelcrud\Helpers;
use Ongoingcloud\Laravelcrud\General\Activity;
use Ongoingcloud\Laravelcrud\General\ModuleConfig;
use Ongoingcloud\Laravelcrud\General\HandlePermission;
use App\Http\Controllers\Controller;
use Ongoingcloud\Laravelcrud\Exceptions\GeneralException;
use Ongoingcloud\Laravelcrud\Models\Role as Module;
use Ongoingcloud\Laravelcrud\Models\Module as Group;
use Ongoingcloud\Laravelcrud\Models\ModuleGroup;
use Auth;
use Illuminate\Http\Request;
use PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use Ongoingcloud\Laravelcrud\Http\Requests\Role\StoreRoleRequest;
use Ongoingcloud\Laravelcrud\Http\Requests\Role\UpdateRoleRequest;
use Ongoingcloud\Laravelcrud\Http\Requests\Role\DeleteRoleRequest;
use Ongoingcloud\Laravelcrud\Http\Requests\Role\ListRoleRequest;
use Ongoingcloud\Laravelcrud\Http\Requests\Role\OnlyRoleRequest;
use Lang;

class RoleController extends Controller {

    public $data = [];        
    
    public $form_view = 'backend.modules.role';
    public $form_export = 'backend.modules.role-table';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function default() {
        
        $this->data = ModuleConfig::roles();
    }

    public function index(ListRoleRequest $request) {
        $this->default(); 
        $model = new Module;
        $this->data['permissions'] = HandlePermission::getPermissionsVue($this->data['dir']);
        
        $this->data['lists'] = Module::latest()->paginate(25);
        $only = new OnlyRoleRequest();
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

        $only = new OnlyRoleRequest();
        if($only->authorize()){
            $lists = $lists->where('created_by', \Auth::user()->id);
        }

        $this->data['list_data'] = $model->list_data();

        if($request->pdf) {
            $this->data['lists'] = $lists->get();

            $data = $this->data;
            
            $pdf = PDF::loadView($this->form_export, compact('data'));        
            return $pdf->download('Role.pdf');
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
            // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="Role.csv"');
            return $writer->save("php://output");
        }

        if($from_delete) {
            return $this->data;
        }

        $this->data['lists'] = $lists->paginate(25);
        return Helpers::successResponse(Lang::get('label.notification.success_message'),$this->data);
    }

    public function create(StoreRoleRequest $request) {   
        $this->default(); 
        
        $this->data['module_list'] = Group::with('module_groups.permissions')->get();

        $this->data['module_extra_list'] = ModuleGroup::wherenull('module_id')->with('permissions')->get();

        return view($this->form_view, ['data'=>$this->data]);
    }

    public function store(StoreRoleRequest $request) {
        $this->default(); 
        $this->validate($request, [
            "name" => "required"
            ]
        );   

        $input = $request->all();
        //[DropdownValue]
        
        \DB::beginTransaction();   
        try {
            if(isset($request->id)) {
                $model = Module::find($request->id);
                $model->permissions()->detach($model->permissions);
                $msg = Helpers::activity($input, $this->data['lang'], $model->toArray());
                Module::find($request->id)->update($input);
            } else {
                $input["created_by"] = \Auth::user()->id;
                $model = Module::Create($input);
                $msg = "<b>".Auth::user()->name."</b> created ".$this->data['dir'].".";
            }
            if(!empty($msg)) {
                Activity::add($msg, $this->data['dir'], $model->id);
            }

            if($request->permission_id){
                foreach($request->permission_id as $role) {
                    $permission = $model->role_permission()->where('role_id', $model->id)->where('permission_id',$role)->first();
                    if(!$permission) {
                        $model->permissions()->attach($role);
                    }
                }    
            }
            
        } catch (\Exception $e) {
            dd($e);
            \DB::rollback();
            return Helpers::errorResponse();
        }
        \DB::commit();

        $message = isset($request->id) ? Lang::get('roles.edit_message') : Lang::get('roles.create_message');
        return Helpers::successResponse($message,$model);
    }

    public function edit($id, UpdateRoleRequest $request) {
        $this->default(); 
        $this->data['id'] = $id;
        $model = Module::findorfail($id);
        $formelement = $model->getAttributes();
        $formelement['_token'] = csrf_token();
        //[DropdownSelectedValue]
        $this->data['fillable'] = $formelement;
        $this->data['fillable']["module_id"] = [];
        $this->data['fillable']["module_group_id"] = [];
        $this->data['fillable']["permission_id"] = $model->role_permission()->where('role_id', $model->id)->pluck('permission_id');
        $this->data['module_list'] = Group::with('module_groups.permissions')->get();

        $this->data['module_extra_list'] = ModuleGroup::wherenull('module_id')->with('permissions')->get();

        $this->data['permissions'] = HandlePermission::getPermissionsVue($this->data['dir']);

        $only = new OnlyRoleRequest();
        if(!$only->authorize() || $model->created_by == \Auth::user()->id) {
            return view($this->form_view, ['data'=>$this->data]);
        } else {
            return "Unauthorized!";
        }    
        
    }

    public function destroy(DeleteRoleRequest $request){
        $this->default(); 
        $this->data['permissions'] = HandlePermission::getPermissionsVue($this->data['dir']);
        \DB::beginTransaction();
        try {
            $model = Module::findorfail($request->id);
            $msg = Auth::user()->name ." deleted data of id : ". $model->id;
            Activity::add($msg, $this->data['dir'], $model->id);
            $model->delete();
        } catch (\Exception $e) {
            \DB::rollback();                        
            return Helpers::errorResponse('Error while deleting role. Try again latter');
        }
        \DB::commit();

        $model = new Module;
        $this->data['lists'] = Module::latest()->paginate(25);
        $only = new OnlyRoleRequest();
        if($only->authorize()){
            $this->data['lists'] = Module::latest()->where('created_by', \Auth::user()->id)->paginate(25);
        }
        $this->data['list_data'] = $model->list_data();
        return Helpers::successResponse(Lang::get('roles.delete_message'),$this->data);
    }
}
