<?php

namespace Ongoingcloud\Laravelcrud\Http\Controllers;

use Ongoingcloud\Laravelcrud\Helpers;
use Ongoingcloud\Laravelcrud\General\Activity;
use Ongoingcloud\Laravelcrud\General\ModuleConfig;
use Ongoingcloud\Laravelcrud\General\HandlePermission;
use App\Http\Controllers\Controller;
use Ongoingcloud\Laravelcrud\Models\Languagetranslet as Module;
use Auth;
use Illuminate\Http\Request;
use PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

use Ongoingcloud\Laravelcrud\Http\Requests\Languagetranslet\StoreLanguagetransletRequest;
use Ongoingcloud\Laravelcrud\Http\Requests\Languagetranslet\UpdateLanguagetransletRequest;
use Ongoingcloud\Laravelcrud\Http\Requests\Languagetranslet\DeleteLanguagetransletRequest;
use Ongoingcloud\Laravelcrud\Http\Requests\Languagetranslet\ListLanguagetransletRequest;
use Ongoingcloud\Laravelcrud\Http\Requests\Languagetranslet\OnlyLanguagetransletRequest;
use Lang;

class LanguagetransletController extends Controller {

    public $data = [];        
    
    public $form_view = 'backend.modules.languagetranslet';
    public $form_export = 'backend.modules.languagetranslet-table';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function default() {
        
        $this->data = ModuleConfig::language_translets();
         // [Module_Data]
    }

    public function index(ListLanguagetransletRequest $request) { 
        $this->default();
        $model = new Module;
        $this->data['permissions'] = HandlePermission::getPermissionsVue($this->data['dir']);

        $this->data['lists'] = Module::latest()->paginate(25);
        $only = new OnlyLanguagetransletRequest();
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

        $only = new OnlyLanguagetransletRequest();
        if($only->authorize()){
            $lists = $lists->where('created_by', \Auth::user()->id);
        }

        $this->data['list_data'] = $model->list_data();

        if($request->pdf) {
            $this->data['lists'] = $lists->get();

            $data = $this->data;
            
            $pdf = PDF::loadView($this->form_export, compact('data'));        
            return $pdf->download('Languagetranslet.pdf');
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
            header('Content-Disposition: attachment; filename="Languagetranslet.csv"');
            return $writer->save("php://output");
        }

        if($from_delete) {
            return $this->data;
        }

        $this->data['lists'] = $lists->paginate(25);
        return Helpers::successResponse(Lang::get('label.notification.success_message'),$this->data);
    }

    public function getLangArrayPagination(Request $request) {
        $this->default();

        $page = ! empty( $request->page ) ? (int) $request->page : 1;
        $lang_value = $this->data['lang_value'];

        if($request->id) {
            $this->editData($request->id);
            $lang_value = $this->data['fillable']['value'];
        }

        $total = count( $lang_value ); //total items in array    
        $limit = 10; //per page    
        $totalPages = ceil( $total/ $limit ); //calculate total pages
        $page = max($page, 1); //get 1 page when $_GET['page'] <= 0
        $page = min($page, $totalPages); //get last page when $_GET['page'] > $totalPages
        $offset = ($page - 1) * $limit;
        if( $offset < 0 ) $offset = 0;

        $data['lang_value_pagination'] = array_slice( $lang_value, $offset, $limit );

        $data['page'] = $page;
        $data['total_pages'] = $totalPages;
        return $data;
    }

    public function create(StoreLanguagetransletRequest $request) {
        $this->default();

        return view($this->form_view, ['data'=>$this->data]);
    }

    public function store(StoreLanguagetransletRequest $request) {
        $this->default();
        
        $this->validate($request, [
                "locale" => "required",
            ]
        );   
        
		// [GridValidation]
        $input = $request->all();
        $input['status'] = 1;
        //[DropdownValue]
        
        \DB::beginTransaction();   
        try {
            if(isset($request->id)) {
                $model = Module::find($request->id);
                
                $model->language_translet_details()->where("languagetranslet_id", $request->id)->delete();
				// [GridDelete]
                $model->update($input);
                
            } else {
                $input["created_by"] = \Auth::user()->id;
                $model = Module::Create($input);
            }
             
            for($i=0; $i < count(array_filter($request->value)); $i++) {

                $model->language_translet_details()->create([
                    "languagetranslet_id" => $model->id,
                    'value' => $request->value[$i],
                    'translation' => isset($request->translation[$request->value[$i]]) ? $request->translation[$request->value[$i]] : "",  
                ]);
            }
            
				// [GridSave]
           
            if(!\File::isDirectory(base_path()."/resources/lang/".$model->locale)){
                \File::makeDirectory(base_path()."/resources/lang/".$model->locale);
            }

            $filesInFolder = \File::allFiles(base_path('resources/lang/en'));
            foreach($filesInFolder as $path) { 
                $file[] = pathinfo($path);
            }

            foreach ($file as $key => $value) {
                $content = file_get_contents($value['dirname']."/".$value['basename']);

                foreach ($model->language_translet_details as $k => $val) {
                    $translate_word = $val->translation ? $val->translation : $val->value;
                    $content = preg_replace("/\\'".preg_quote($val->value,'/')."\\'/","'".$translate_word."'", $content);

                    file_put_contents(base_path()."/resources/lang/".$model->locale."/".$value['basename'], $content);
                }
            }
        
        } catch (\Exception $e) {
            \DB::rollback();
            return Helpers::errorResponse();
        }
        \DB::commit();

        $message = isset($request->id) ? Lang::get('language_translets.edit_message') : Lang::get('language_translets.create_message');
        return Helpers::successResponse($message,$model);
    }

    public function editData($id) {
        $model = Module::findorfail($id);
        $formelement = $model->getAttributes();
        $formelement['_token'] = csrf_token();
        
        // [DropdownSelectedValue]

        if(count($model->language_translet_details) > 0 ) {
            $this->data["language_translet_details_row"] = [];
            $this->data["language_translet_detailsrow_count"] = count($model->language_translet_details) - 1;
            foreach ($model->language_translet_details as $key => $value) {
                $this->data["language_translet_details_row"][] = $key;
                
                $formelement['translation'][$value->value] = $value->translation;
                
            }
        } else {
            $formelement['value'][] = "";
            $formelement['translation'][] = "";
            
        }

        $this->data['fillable'] = $formelement;

        foreach ($this->data['lang_value'] as $key => $value) {
            if(empty($formelement['translation'][$value])) {
                $this->data['fillable']['value'][] = $value;
            }
        }

        foreach ($this->data['lang_value'] as $key => $value) {
            if(!empty($formelement['translation'][$value])) {
                $this->data['fillable']['value'][] = $value;
            }
        }
    }

    public function edit($id, UpdateLanguagetransletRequest $request) {
        $this->default();
        
        $this->data['id'] = $id;
        $this->editData($id);
        // [GridEdit]

        $this->data['permissions'] = HandlePermission::getPermissionsVue($this->data['dir']);

        $only = new OnlyLanguagetransletRequest();
        if(!$only->authorize() || $model->created_by == \Auth::user()->id) {
            return view($this->form_view, ['data'=>$this->data]);
        } else {
            return "Unauthorized!";
        }
    }

    public function destroy(DeleteLanguagetransletRequest $request){
        $this->default();
        
        $this->data['permissions'] = HandlePermission::getPermissionsVue($this->data['dir']);
        
        \DB::beginTransaction();
        try {
            $model = Module::findorfail($request->id);
            $msg = "<b>".Auth::user()->name."</b> deleted data of id : ". $model->id;
            Activity::add($msg, $this->data['dir'], $model->id);
            $model->language_translet_details()->where("languagetranslet_id", $request->id)->delete();
				// [GridDelete]
            $model->delete();
        } catch (\Exception $e) {
            \DB::rollback();                        
            return Helpers::errorResponse('Error while deleting languagetranslet. Try again latter');
        }
        \DB::commit();

        $model = new Module;
        $this->data['lists'] = Module::latest()->paginate(25);
        $only = new OnlyLanguagetransletRequest();
        if($only->authorize()){
            $this->data['lists'] = Module::latest()->where('created_by', \Auth::user()->id)->paginate(25);
        }
        $this->data['list_data'] = $model->list_data();
        return Helpers::successResponse(Lang::get('language_translets.delete_message'),$this->data);
    }
}
