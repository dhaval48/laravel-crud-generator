<?php 

namespace Ongoingcloud\Laravelcrud;

use Ongoingcloud\Laravelcrud\Http\Controllers\CommonController;

class Helpers {

    public static function attachFile($request){
        $input = $request->all();
        foreach ($input as $key => $value) {
            if($request->hasFile($key)) {
                $input[$key] = Helpers::uploadFille($request->$key);
            }
        }
        return $input;
    }

    public static function uploadFille($file){
        $destinationPath = storage_path() . '/app/public/';

        $file_name = date('mdYHis') . uniqid() . "." .$file->getClientOriginalExtension();

        $file->move($destinationPath, $file_name);

        return $destinationPath.'/'.$file_name;
    }
    
	public static function errorResponse($message = 'Somethings goes wrong.', $errors = [], $code = 422) {
	    if($message == 'Somethings goes wrong.') {
	        $message = \Lang::get('label.notification.error_message');
	    }
	    $response = [
	        'meta' => [
	            'code' => $code,
	            'message' => $message,
	        ],
	        'errors'      => $errors,
	        'data' => []
	    ];
	    return \Response::json($response, $code);
	}

	public static function successResponse($message = 'Success!',$data = [], $code = 200) {
	    if($message == 'Success!') {
	        $message = \Lang::get('label.notification.success_message');
	    }
	    $response = [
	        'meta' => [
	            'code' => $code,
	            'message' => $message
	        ],
	        'errors'      => [],
	        'data' => $data
	    ];
	    return \Response::json($response, $code);
	}

	public static function formatDeleteFillable() {
	    $out['id'] = '';
	    $out['_token'] = csrf_token();
	    return $out;
	}  

	public static function isUnique($table,$unique,$value,$item_type = '',$item_type_name = '') {
        if($item_type != ''){
            $result = \DB::table($table)
                ->where($unique, $value)
                ->where($item_type, $item_type_name)              
                ->wherenull('deleted_at')
                ->first();

        } else {
            $result = \DB::table($table)
                ->where($unique, $value)              
                ->wherenull('deleted_at')
                ->first();
        }

        if (empty($result)) {
            return false;
        }
        return true;
    }

	public static function isDuplicate($table,$unique,$value,$id,$item_type = '',$item_type_name = '') {
        // dd($unique);
        if($item_type != ''){
            $result = \DB::table($table)
                ->where($unique, $value)
                ->where($item_type, $item_type_name)              
                ->where('id', '!=', $id)
                ->wherenull('deleted_at')
                ->first();

        } else {
            $result = \DB::table($table)
                ->where($unique, $value)
                ->where('id', '!=', $id)
                ->wherenull('deleted_at')
                ->first();
        }
        
        // dd($result);
        if (empty($result)) {
            return false;
        }
        return true;
    }
	    
    public static function parseDBdate($date) {     
        return \Carbon\Carbon::parse($date)->format('Y-m-d');
    }    
	    
    public static function parseDate($date) {     
        return \Carbon\Carbon::parse($date)->format('m/d/Y');
    }    
    public static function activityRow($request, $count, $model = "") { 
        $msg = [];
        for($i=0; $i < $count;) {
            $string = "";
            unset($model[$i]['created_at']);
            unset($model[$i]['updated_at']);
            if(isset($model[$i])) {
                foreach ($model[$i] as $key => $value) {
                    if(isset($request[$key])) {
                        if($request[$key][$i] != $value) {
                            $string .= $key ." from <b>". $value ."</b> to <b>" .$request[$key][$i]."</b> ";
                        }                              
                    }
                }
            }
            $i++;

            if($i == 1) {
                $rows = "1st";
            }
            if($i == 2) {
                $rows = "2nd";
            }
            if($i == 3) {
                $rows = "3rd";
            }
            if($i > 3) {
                $rows = $i."th";
            }

            if($string != "") {
                $msg[] = "<b>".\Auth::user()->name."</b> changed <b>".$rows." rows </b>value of ". $string. ".";
            } else {
                // $msg[] = "<b>".Auth::user()->name."</b> has nothing to changed in ".$rows." row.";
            }
        }
        return $msg;
    }

    public static function activity($request, $lang, $model = "") { 
        $string = "";
        $msg = "";
        unset($model['created_at']);
        unset($model['updated_at']);
        
        foreach ($model as $key => $value) {
            if(isset($request[$key]) && $request[$key] != $value) {
                if($key == "date" || $key == "due_date" || $key == "delivery_date") {
                    $value = Helpers::parseDate($value);
                    $request[$key] = Helpers::parseDate($request[$key]);
                }
                $string .= "<b>".$lang[$key] ."</b> from <b>". $value ."</b> to <b>" .$request[$key] ."</b> ";
            }
        }
        if($string == "") {
            // $msg = "<b>".Auth::user()->name."</b> has nothing to changed.";
        } else {
            $msg = "<b>".\Auth::user()->name."</b> changed value of ". $string. ".";
        }

        return $msg;
    }

    public static function getFilePath($file) {
        return storage_path().'/app/public/'.str_replace('storage', '', $file);
    }

    public static function getFileBase64($path) {
        if(file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
    }
	 
    public static function getRelation($value, $list_data) {
        $list_data = explode('.', $list_data);
        if(isset($list_data[1])) {
            $value = $value[$list_data[0]][$list_data[1]];
        } else {
            $value = $value[$list_data[0]];
        }
        return $value;
    }

    public static function LangCode($code=false) {
        $code = trim($code);
        $language_codes = array(
            'en' => 'English' ,
            'aa' => 'Afar' ,
            'ab' => 'Abkhazian' ,
            'af' => 'Afrikaans' ,
            'am' => 'Amharic' ,
            'ar' => 'Arabic' ,
            'as' => 'Assamese' ,
            'ay' => 'Aymara' ,
            'az' => 'Azerbaijani' ,
            'ba' => 'Bashkir' ,
            'be' => 'Byelorussian' ,
            'bg' => 'Bulgarian' ,
            'bh' => 'Bihari' ,
            'bi' => 'Bislama' ,
            'bn' => 'Bengali/Bangla' ,
            'bo' => 'Tibetan' ,
            'br' => 'Breton' ,
            'ca' => 'Catalan' ,
            'co' => 'Corsican' ,
            'cs' => 'Czech' ,
            'cy' => 'Welsh' ,
            'da' => 'Danish' ,
            'de' => 'German' ,
            'dz' => 'Bhutani' ,
            'el' => 'Greek' ,
            'eo' => 'Esperanto' ,
            'es' => 'Spanish' ,
            'et' => 'Estonian' ,
            'eu' => 'Basque' ,
            'fa' => 'Persian' ,
            'fi' => 'Finnish' ,
            'fj' => 'Fiji' ,
            'fo' => 'Faeroese' ,
            'fr' => 'French' ,
            'fy' => 'Frisian' ,
            'ga' => 'Irish' ,
            'gd' => 'Scots/Gaelic' ,
            'gl' => 'Galician' ,
            'gn' => 'Guarani' ,
            'gu' => 'Gujarati' ,
            'ha' => 'Hausa' ,
            'hi' => 'Hindi' ,
            'hr' => 'Croatian' ,
            'hu' => 'Hungarian' ,
            'hy' => 'Armenian' ,
            'ia' => 'Interlingua' ,
            'ie' => 'Interlingue' ,
            'ik' => 'Inupiak' ,
            'in' => 'Indonesian' ,
            'is' => 'Icelandic' ,
            'it' => 'Italian' ,
            'iw' => 'Hebrew' ,
            'ja' => 'Japanese' ,
            'ji' => 'Yiddish' ,
            'jw' => 'Javanese' ,
            'ka' => 'Georgian' ,
            'kk' => 'Kazakh' ,
            'kl' => 'Greenlandic' ,
            'km' => 'Cambodian' ,
            'kn' => 'Kannada' ,
            'ko' => 'Korean' ,
            'ks' => 'Kashmiri' ,
            'ku' => 'Kurdish' ,
            'ky' => 'Kirghiz' ,
            'la' => 'Latin' ,
            'ln' => 'Lingala' ,
            'lo' => 'Laothian' ,
            'lt' => 'Lithuanian' ,
            'lv' => 'Latvian/Lettish' ,
            'mg' => 'Malagasy' ,
            'mi' => 'Maori' ,
            'mk' => 'Macedonian' ,
            'ml' => 'Malayalam' ,
            'mn' => 'Mongolian' ,
            'mo' => 'Moldavian' ,
            'mr' => 'Marathi' ,
            'ms' => 'Malay' ,
            'mt' => 'Maltese' ,
            'my' => 'Burmese' ,
            'na' => 'Nauru' ,
            'ne' => 'Nepali' ,
            'nl' => 'Dutch' ,
            'no' => 'Norwegian' ,
            'oc' => 'Occitan' ,
            'om' => '(Afan)/Oromoor/Oriya' ,
            'pa' => 'Punjabi' ,
            'pl' => 'Polish' ,
            'ps' => 'Pashto/Pushto' ,
            'pt' => 'Portuguese' ,
            'qu' => 'Quechua' ,
            'rm' => 'Rhaeto-Romance' ,
            'rn' => 'Kirundi' ,
            'ro' => 'Romanian' ,
            'ru' => 'Russian' ,
            'rw' => 'Kinyarwanda' ,
            'sa' => 'Sanskrit' ,
            'sd' => 'Sindhi' ,
            'sg' => 'Sangro' ,
            'sh' => 'Serbo-Croatian' ,
            'si' => 'Singhalese' ,
            'sk' => 'Slovak' ,
            'sl' => 'Slovenian' ,
            'sm' => 'Samoan' ,
            'sn' => 'Shona' ,
            'so' => 'Somali' ,
            'sq' => 'Albanian' ,
            'sr' => 'Serbian' ,
            'ss' => 'Siswati' ,
            'st' => 'Sesotho' ,
            'su' => 'Sundanese' ,
            'sv' => 'Swedish' ,
            'sw' => 'Swahili' ,
            'ta' => 'Tamil' ,
            'te' => 'Tegulu' ,
            'tg' => 'Tajik' ,
            'th' => 'Thai' ,
            'ti' => 'Tigrinya' ,
            'tk' => 'Turkmen' ,
            'tl' => 'Tagalog' ,
            'tn' => 'Setswana' ,
            'to' => 'Tonga' ,
            'tr' => 'Turkish' ,
            'ts' => 'Tsonga' ,
            'tt' => 'Tatar' ,
            'tw' => 'Twi' ,
            'uk' => 'Ukrainian' ,
            'ur' => 'Urdu' ,
            'uz' => 'Uzbek' ,
            'vi' => 'Vietnamese' ,
            'vo' => 'Volapuk' ,
            'wo' => 'Wolof' ,
            'xh' => 'Xhosa' ,
            'yo' => 'Yoruba' ,
            'zh-Hans' => 'Chinese (Simplified)',
            'zh-Hant' => 'Chinese (Traditional)',
            'zu' => 'Zulu'
        );
        if(!$code) {
            return $language_codes;
        }
        return isset($language_codes[$code]) ? $language_codes[$code] : '';
    }

    public static function existTabale($request, $model=false) {
        $exist_tables = CommonController::getTable($request)->toArray();

        if(!isset($request->id)) {
            if(in_array($request->table_name, $exist_tables)) {
                return true;
            }
        }

        if($model && $model->table_name != $request->table_name) {
            if(in_array($request->table_name, $exist_tables)) {
                return true;
            }
        }

        return false;
    }

    public static function makeControllerName($request) {
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

    public static function ifExistFile($request, $model) {
        $controller_name = Helpers::makeControllerName($request);

        if(!isset($request->id)) {
            if(file_exists(base_path().'/vendor/ongoingcloud/laravelcrud/src/Models/'.ucfirst($controller_name).'.php')) {
                return true;
            }

            if(file_exists(base_path().'/app/Models/'.ucfirst($controller_name).'.php')) {
                return true;
            }
        }

        if($model && $model->main_module != $request->main_module) {
            if(file_exists(base_path().'/vendor/ongoingcloud/laravelcrud/src/Models/'.ucfirst($controller_name).'.php')) {
                return true;
            }

            if(file_exists(base_path().'/app/Models/'.ucfirst($controller_name).'.php')) {
                return true;
            }
        }
        return false;
    }


}