<?php

namespace Ongoingcloud\Laravelcrud\Http\Controllers;

use Ongoingcloud\Laravelcrud\Helpers;
use Ongoingcloud\Laravelcrud\General\Activity;
use Ongoingcloud\Laravelcrud\General\ModuleConfig;
use Ongoingcloud\Laravelcrud\General\HandlePermission;
use App\Http\Controllers\Controller;
use Ongoingcloud\Laravelcrud\Exceptions\GeneralException;
use Ongoingcloud\Laravelcrud\Models\User as Module;
use Auth;
use Illuminate\Http\Request;
use PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

use Ongoingcloud\Laravelcrud\Http\Requests\User\StoreUserRequest;
use Ongoingcloud\Laravelcrud\Http\Requests\User\UpdateUserRequest;
use Ongoingcloud\Laravelcrud\Http\Requests\User\DeleteUserRequest;
use Ongoingcloud\Laravelcrud\Http\Requests\User\ListUserRequest;
use Ongoingcloud\Laravelcrud\Http\Requests\User\OnlyUserRequest;
use Lang;

class UserController extends Controller {

    public $data = [];        
    
    public $form_view = 'backend.modules.user';
    public $form_export = 'backend.modules.user-table';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function default() {
        
        $this->data = ModuleConfig::users();
        $this->data['role_module'] = ModuleConfig::roles();
    }

    public function index(ListUserRequest $request) { 
        $this->default(); 
        $model = new Module;
        $this->data['permissions'] = HandlePermission::getPermissionsVue($this->data['dir']);

        $this->data['lists'] = Module::latest()->paginate(25);
        $only = new OnlyUserRequest();
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

        $only = new OnlyUserRequest();
        if($only->authorize()){
            $lists = $lists->where('created_by', \Auth::user()->id);
        }

        $this->data['list_data'] = $model->list_data();

        if($request->pdf) {
            $this->data['lists'] = $lists->get();

            $data = $this->data;
            
            $pdf = PDF::loadView($this->form_export, compact('data'));        
            return $pdf->download('User.pdf');
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
            header('Content-Disposition: attachment; filename="User.csv"');
            return $writer->save("php://output");

            // Excel::create('User',function($excel) use($request,$data)  {
            //     $excel->sheet('Sheet 1',function($sheet) use($request,$data)  {
            //         $sheet->loadView($this->form_export, compact('data'));
            //     });
            // })->export('csv');
        }

        if($from_delete) {
            return $this->data;
        }

        $this->data['lists'] = $lists->paginate(25);
        return Helpers::successResponse(Lang::get('label.notification.success_message'),$this->data);
    }

    public function create(StoreUserRequest $request) {   
        $this->default(); 

        return view($this->form_view, ['data'=>$this->data]);
    }

    public function store(StoreUserRequest $request) {
        $this->default(); 
        $this->validate($request, [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255'],
            ]
        );   

        if(isset($request->id)) {
            if(Helpers::isDuplicate('users','email',$request->email, $request->id)) {
                return Helpers::errorResponse('That email already exists. Please choose a different email');
            }
        } else {
            $this->validate($request, [
                    'password' => ['required', 'string', 'min:6', 'confirmed'],
                ]
            ); 
            if(Helpers::isUnique('users','email',$request->email)) {
                return Helpers::errorResponse('That email already exists. Please choose a different email');
            }
        }

        $input = $request->all();
        //[DropdownValue]
        
        \DB::beginTransaction();   
        try {
            if(isset($request->id)) {
                $model = Module::find($request->id);
                $model->roles()->detach($model->roles);
                $msg = Helpers::activity($input, $this->data['lang'], $model->toArray());
                Module::find($request->id)->update($input);
            } else {
                $input['password'] = \Hash::make($input['password']);
                $input["created_by"] = \Auth::user()->id;

                $model = Module::create($input);
                $msg = "<b>".Auth::user()->name."</b> created ".$this->data['dir'].".";
            }
            if(!empty($msg)) {
                Activity::add($msg, $this->data['dir'], $model->id);
            }

            if($request->role_id) {
                // foreach($request->role_id as $role) {
                    $model->roles()->attach($request->role_id);
                // }    
            }
        } catch (\Exception $e) {
            dd($e);
            \DB::rollback();
            return Helpers::errorResponse();
        }
        \DB::commit();

        $message = isset($request->id) ? Lang::get('users.edit_message') : Lang::get('users.create_message');
        return Helpers::successResponse($message, $model);
    }

    public function edit($id, UpdateUserRequest $request) {
        $this->default(); 

        $this->data['id'] = $id;
        $model = Module::findorfail($id);
        $formelement = $model->getAttributes();
        $formelement['_token'] = csrf_token();

        //[DropdownSelectedValue]
        $this->data['fillable'] = $formelement;
        $this->data['fillable']['password_confirmation'] = "";
        $this->data['fillable']['role_id'] = isset($model->roles[0]) ? $model->roles[0]->id : '';

        $this->data['title'] = 'Edit User';        

        $this->data['permissions'] = HandlePermission::getPermissionsVue($this->data['dir']);

        $only = new OnlyUserRequest();
        if(!$only->authorize() || $model->created_by == \Auth::user()->id) {
            return view($this->form_view, ['data'=>$this->data]);
        } else {
            return "Unauthorized!";
        }
    }

    public function destroy(DeleteUserRequest $request){
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
            return Helpers::errorResponse();
        }
        \DB::commit();

        $model = new Module;
        $this->data['lists'] = Module::latest()->paginate(25);
        $only = new OnlyUserRequest();
        if($only->authorize()){
            $this->data['lists'] = Module::latest()->where('created_by', \Auth::user()->id)->paginate(25);
        }

        $this->data['list_data'] = $model->list_data();
        return Helpers::successResponse(Lang::get('users.delete_message'),$this->data);
    }

    public function createChangePassword(Request $request) {
        $this->default(); 
        $this->data['store_route'] = route('changepassword.store');
        $this->data["fillable"]["current_password"] = "";
        return view('backend.modules.changepassword', ['data'=>$this->data]);
    }

    public function postChangePassword(Request $request) {
        $this->default(); 
        if(Auth::Check())
        {
            $this->validate($request, [
                    'current_password' => 'required',
                    'password' => 'required|same:password',
                    'password_confirmation' => 'required|same:password',
                ],
                [
                    'current_password.required' => 'Please enter current password',
                    'password.required' => 'Please enter password',
                ]
            ); 
            $input = $request->All();

            $current_password = \Auth::user()->password;           
            if(\Hash::check($input['current_password'], $current_password)) {

                \Auth::user()->update(['password' => \Hash::make($input['password'])]); 
                return Helpers::successResponse(Lang::get('users.change_password_success'));
            } else {             

                return Helpers::errorResponse('Please enter correct current password');
            }
                    
        } else {
            return Helpers::errorResponse();
        }    
    }
}
