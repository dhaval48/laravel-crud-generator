@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 mtop-10">
            <div class="card">
                <div class="card-header">
                    <h5>
                        <a class="card-title" href="{{ $data['list_route'] }}"> 
                        {{ $data['lang']['change_password'] }}
                        </a>
                    </h5>
                </div>

                <div class="card-body"> 
                    <changepassword-view :module="{{json_encode($data)}}"></changepassword-view>
                </div>
            </div>
        </div>
    </div>            
</div> 
@endsection