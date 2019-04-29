@extends('crudpack::backend.layouts.app')

@section('after-styles')
	
@endsection
@section('content')
<div id="content">
    <section>
        @if(!isset($data['lists'])) 
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <a href="{{ $data['list_route'] }}"> 
                                <i class="fa fa-long-arrow-left" aria-hidden="true"></i>
                            &nbsp;&nbsp;
                            <h3 class="card-title">{{ $data['id'] != 0 ? $data['lang']['edit_title'] : $data['lang']['create_title'] }}</h3>
                            </a>
                        </div>

                        <div class="card-body">
    				        <permission_modules-view :module="{{json_encode($data)}}"></permission_modules-view>
    				    </div>
                    </div>
                </div>
            </div>            
        @else
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{$data['lang']['list']}}</h3>
                        </div>
                        
                        <permission_modules-view :module="{{json_encode($data)}}"></permission_modules-view>
                        
                    </div>
                </div>
            </div>           
        @endif  
    </section>
</div><!--end #content-->


{{-- [Modal_path] --}}
@endsection
@section('after-scripts')
	
@endsection
