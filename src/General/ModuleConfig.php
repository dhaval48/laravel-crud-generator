<?php 

namespace Ongoingcloud\Laravelcrud\General;
use Lang;

class ModuleConfig {

	public static function users() {
        return include 'ModuleConfig/user.php';
    }

	public static function roles() {
		return include 'ModuleConfig/role.php';
    }

    public static function permission_modules() {
		return include 'ModuleConfig/permissionmodule.php';
    }

    public static function form_modules() {
		return include 'ModuleConfig/formmodule.php';
    }

    public static function grid_modules() {
		return include 'ModuleConfig/gridmodule.php';
    }

    public static function api_modules() {
        return include 'ModuleConfig/apimodule.php';
    }	

    public static function language_translets() {
        return include 'ModuleConfig/languagetranslet.php';
    }


	// [Moduleconfig]
}