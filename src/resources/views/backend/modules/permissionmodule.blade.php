@extends('layouts.app')

@section('content')
<div class="container">
    <section>
        <div class="row justify-content-center">
            <div class="col-md-12 mtop-10">
                <div class="card">
                
                @if(!isset($data['lists'])) 
                    <div class="card-header">
                        <a class="card-title h4" href="{{ $data['list_route'] }}"> 
                            {{ $data['id'] != 0 ? $data['lang']['edit_title'] : $data['lang']['create_title'] }}
                        </a>
                    </div>

                @else

                    <div class="card-header h4">{{ $data['lang']['list'] }}</div>                   
                   
                @endif
                    <div class="card-body">
                        <permission_modules-view :module="{{json_encode($data)}}"></permission_modules-view>
                    </div> 
                </div>
            </div>
        </div>  
    </section>
</div>


{{-- [Modal_path] --}}
@endsection
