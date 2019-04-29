@extends('backend.layouts.app')

@section('after-styles')
	
@endsection
@section('content')
<div id="content">
    <section>
        @if(!isset($data['lists'])) 
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <a href="{{ $data['list_route'] }}"> 
                                <i class="fa fa-long-arrow-left" aria-hidden="true"></i>
                            &nbsp;&nbsp;
                            <h3 class="box-title">{{ $data['id'] != 0 ? $data['lang']['edit_title'] : $data['lang']['create_title'] }}</h3>
                            </a>
                        </div>

                        <div class="box-body">
    				        <grid_modules-view :module="{{json_encode($data)}}"></grid_modules-view>
    				    </div>
                    </div>
                </div>
            </div>            
        @else
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">{{$data['lang']['list']}}</h3>
                        </div>
                        
                        <grid_modules-view :module="{{json_encode($data)}}"></grid_modules-view>
                        
                    </div>
                </div>
            </div>           
        @endif  
    </section>
</div><!--end #content-->

{{-- @include("backend.modal.form_modules",$data['form_modules_module']) --}}
@include("backend.modal.permission_modules",$data['permission_modules_module'])



{{-- [Modal_path] --}}
@endsection
@section('after-scripts')
	
@endsection
