<?php

namespace Ongoingcloud\Laravelcrud\Http\Controllers;

use Ongoingcloud\Laravelcrud\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Ongoingcloud\Laravelcrud\Models\FileUpload;
use Ongoingcloud\Laravelcrud\Models\FileUploadDetail;

class CommonController extends Controller
{	

	public static function fileUpload(Request $request) {
        \DB::beginTransaction();   
        try {
	        $files = $request->file('upload_files');

	        $destinationPath = storage_path() . '/app/public/';

	        if(isset($request->file_detail_id) && count($request->file_detail_id) > 0) {
	        	for ($i=0; $i < count($request->file_detail_id); $i++) { 	 
		       		if($request->file_name[$i] != 'undefined' && !empty($request->file_name[$i])) {

		       			$file_detail = FileUploadDetail::find($request->file_detail_id[$i]);
		       			if($file_detail) {
		       				$extension =  explode('.', $request->file_name[$i]);

			        		if(!isset($extension[1])) {
			        			$ext =  explode('.', $file_detail->name);
			        			$extension[1] = $ext[1];
			        		}

			       			$file_detail->update(['name' => $extension[0]. '.' .$extension[1]]);
		       			}
		       		}
		        }
	        }
	        if(\Auth::user()) {
				$file_upload = \Auth::user()->file_upload()->where('type', $request->type)->where('type_id', $request->type_id)->first();

	        } else {
				$file_upload = FileUpload::where('type', $request->type)->where('type_id', $request->type_id)->first();        	
	        }
	        if($files) {

	        	if(!$file_upload) {
	        		if(\Auth::user()) { 
				        $model_id = \Auth::user()->file_upload()->create($request->all())->id;
				    } else {
				        $model_id = FileUpload::create($request->all())->id;
				    }
		        } else {
		        	$model_id = $file_upload->id;
		        }
	        	foreach ($files as $key => $file) {
		        	$file_name = date('mdYHis') . uniqid() . "." .$file->getClientOriginalExtension();

		        	$name = $file->getClientOriginalName();

		        	if(!empty($request->file_name[$key]) && $request->file_name[$key] != "undefined") {
		        		$extension =  explode('.', $request->file_name[$key]);

		        		if(!isset($extension[1])) {
		        			$extension[1] = $file->getClientOriginalExtension();
		        		}
		        		$name = $extension[0]. '.' .$extension[1];
		        	}
		            $file->move($destinationPath, $file_name);
		            FileUploadDetail::create(['file_upload_id' => $model_id,
		            						  'name' => $name,
		            						  'path_name' => $file_name,
		        							]);
		        }
	        }

	    } catch (\Exception $e) {
	    	echo $e;
            \DB::rollback();
            return Helpers::errorResponse();
        }
        \DB::commit();

        return Helpers::successResponse("Successfully File Uploaded");
    }

    public static function getFile(Request $request) {
		if(\Auth::user()) {
			$file = FileUpload::latest()->where('user_id', \Auth::user()->id)->where('type', $request->type)->where('type_id', $request->id)->first();
		} else {
			$file = FileUpload::latest()->where('type', $request->type)->where('type_id', $request->id)->first();
		}

		if($file) {
			return [$file->id, $file->file_upload_details()->get()];
		}
	}

	public static function fileDelete(Request $request) {
		\DB::beginTransaction();   
        try {

        	$destinationPath = storage_path() . '/app/public';
			$file = FileUploadDetail::findorfail($request->file_detail_id);
			if(file_exists($destinationPath."/".$file->path_name)) {
                unlink($destinationPath."/".$file->path_name);
            }
            $file->delete();
		 } catch (\Exception $e) {
            \DB::rollback();
            return Helpers::errorResponse();
        }
        \DB::commit();
		return Helpers::successResponse("Successfully File Deleted");
	}

	public static function fileDownload(Request $request) {
        $attachment = FileUploadDetail::find($request->file_detail_id);        
        $path = Helpers::getFilePath($attachment->path_name);
        if(file_exists($path)) {        	
            return response()->download($path);
        }        
	}

	public function getActivity(Request $request) {

		return \DB::table('activities')->latest()->where('type', $request->type)->where('type_id', $request->id)->get();

	}	

	public function getRoles(Request $request){
		return \DB::table('roles')->latest()->wherenull('deleted_at')->pluck('name', 'id');
	}

	public function getParent_form(Request $request) {

		return \DB::table('form_modules')->latest()->wherenull('deleted_at')->wherenull('parent_form')->pluck('main_module','main_module');

	}

	public static function getTable(Request $request) {
		return collect(\DB::select('show tables'))->map(function ($val) {
            foreach ($val as $key => $tbl) {
                return $tbl;
            }
        });
	}

	public function getTable_data(Request $request) {

		return \DB::getSchemaBuilder()->getColumnListing($request->q);
	}

	public function getParent_module(Request $request) {

		return \DB::table('permission_modules')->latest()->wherenull('deleted_at')->pluck('name','name');

	}

	public function getParent_api_form(Request $request) {

		return \DB::table('api_modules')->latest()->select('main_module as label','main_module as value')->where('main_module','like',"%$request->q%")->wherenull('deleted_at')->get();

	}
	public function getParent_api_table(Request $request) {

		return \DB::table('api_modules')->latest()->select('table_name as label','table_name as value')->where('table_name','like',"%$request->q%")->wherenull('deleted_at')->get();

	}

	public function userLangUpdate(Request $request) {
		\App::setLocale($request->locale);
		\Auth::user()->update(['locale' => $request->locale]);
		return back();
	}
	// [Function]
}