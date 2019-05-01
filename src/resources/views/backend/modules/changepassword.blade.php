@extends('layouts.app')

@section('content')
<div class="container">
    <section>
        <div class="row justify-content-center">
            <div class="col-md-12 mtop-10">
                <div class="card">
                    <div class="card-header">
                        <a class="card-title h4" href="{{ $data['list_route'] }}"> 
                            {{ $data['lang']['change_password'] }}
                        </a>
                    </div>

                    <div class="card-body"> 
                        <changepassword-view :module="{{json_encode($data)}}"></changepassword-view>
                    </div>
                </div>
            </div>
        </div>            
    </section>
</div> 
@endsection