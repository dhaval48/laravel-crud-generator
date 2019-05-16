@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 mtop-10">
            <div class="card">
            
            @if(!isset($data['lists'])) 
                <div class="card-header">
                    {{ $data['id'] != 0 ? $data['lang']['edit_title'] : $data['lang']['create_title'] }}
                </div>

            @else

                <h5><div class="card-header">{{ $data['lang']['list'] }}</div></h5>                   
               
            @endif
                <div class="card-body">
                    <permission_modules-view :module="{{json_encode($data)}}"></permission_modules-view>
                </div> 
            </div>
        </div>
    </div>  
</div>


{{-- [Modal_path] --}}
@endsection
