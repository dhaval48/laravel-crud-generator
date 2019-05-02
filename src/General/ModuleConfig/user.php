<?php

$formelements = ["name" => "",
                'email' => "",
                'password' => '',
                'password_confirmation' => '',
                'role_id' => '',
            ];

        $formelements["_token"] = csrf_token();
        
        $data =  [
                'lang' => Lang::get('users'),
                'common' => Lang::get('label.common'),
                  // 'title' => Lang::get('user.create_title'),
                  // 'list' => Lang::get('user.list'),
                'dir'  => 'user',
                'id' => 0,
                'is_visible' => true,
                  // 'button_text' => Lang::get('user.save'),
                'list_route'  =>  route('user.index'),
                'store_route'  => route('user.store'),
                'paginate_route'  => route('user.paginate'),
                'edit_route'  => route('user.edit',''),
                'create_route'  => route('user.create'),
                'destory_route' => route('user.destroy'),
                'get_activity' => route('get.activity'),
                'role_search' => route('get.role'),
                
            ];
		$data["fillable"] = $formelements;
		return $data;
		