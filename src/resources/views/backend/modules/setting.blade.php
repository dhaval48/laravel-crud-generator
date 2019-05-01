@extends('layouts.app')

@section('content')
<div class="container">
    <section>
        <div class="row justify-content-center">
            <div class="col-md-12 mtop-10">
                <div class="card">
                    <div class="card-header h4">Registration Setting</div>                   
                    <div class="card-body">
		    			<settings-view :module="{{json_encode($data)}}"></settings-view>
		        	</div> 
                </div>
            </div>
        </div>  
    </section>
</div>


{{-- [Modal_path] --}}
@endsection
