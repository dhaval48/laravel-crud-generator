<?php

namespace App\Http\Controllers\API;

use Ongoingcloud\Laravelcrud\Helpers;
use App\Http\Controllers\Controller;
use App\Models\[UNAME] as Module;
use Illuminate\Http\Request;
use Ongoingcloud\Laravelcrud\Http\Controllers\CommonController;
use Ongoingcloud\Laravelcrud\Models\FileUploadDetail;

class [UNAME]Controller extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('locale:en');
    }

    public function index(Request $request) { 

        $[MODULE] = Module::latest()->paginate(25);
        return Helpers::successResponse('',$[MODULE]);
    }

    public function store(Request $request) {
        $this->validate($request, [
                [VALIDATION]
            ]
        );   
        // [GridValidation]
        $input = $request->all();
        
        \DB::beginTransaction();   
        try {
            if(isset($request->id)) {
                $model = Module::find($request->id);
                // [GridDelete]
                $model->update($input);
            } else {
                $model = Module::Create($input);
            }
            // [GridSave]
            $request->request->add(['type' => '[MODULE]', 'type_id' => $model->id]);
            CommonController::fileUpload($request);
        } catch (\Exception $e) {
            \DB::rollback();
            return Helpers::errorResponse();
        }
        \DB::commit();

        $message = isset($request->id) ? "[ULABEL] Updated!" : "[ULABEL] Created!";
        return Helpers::successResponse($message,$model);
    }

    public function edit($id, Request $request) {
        $model = Module::findorfail($id);
        $formelement = $model->getAttributes();        
        
        // [GridEdit]
        $request->request->add(['type' => '[MODULE]', 'type_id' => $model->id]);
        $file = CommonController::getFile($request);
        if($file) {
            $formelement['file_id'] = $file[0];
            foreach ($file[1] as $key => $value) {
                $attachment = FileUploadDetail::find($value->id);        
                $path = Helpers::getFilePath($attachment->path_name);
                $formelement['file_detail'][$key] = $value;
                $formelement['file_detail'][$key]['path'] = Helpers::getFileBase64($path);
            }
        }
        return Helpers::successResponse('',$formelement);
    }

    public function destroy(Request $request){
        
        \DB::beginTransaction();
        try {
            $model = Module::findorfail($request->id);
            // [GridDelete]
            $model->delete();
        } catch (\Exception $e) {
            \DB::rollback();                        
            return Helpers::errorResponse('Error while deleting [ULABEL]. Try again latter');
        }
        \DB::commit();
        return Helpers::successResponse("[ULABEL] Deleted!");
    }
}
