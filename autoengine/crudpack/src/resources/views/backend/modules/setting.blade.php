@extends('backend.layouts.app')

@section('after-styles')
	
@endsection
@section('content')

<div id="content"> 
    <div class="row">
        <settings-view :module="{{json_encode($data)}}"></settings-view>
    </div>          
</div>


{{-- [Modal_path] --}}
@endsection
@section('after-scripts')
	
@endsection
