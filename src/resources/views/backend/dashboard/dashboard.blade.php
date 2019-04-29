@extends('backend.layouts.app')

@section('after-styles')
	
@endsection
@section('content')
<div id="content">
    <section>
    	<div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Dashboard</h3>
                    </div>
    
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="info-box">
                                    <span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Users</span>
                                        <span class="info-box-number">{{$total_user}}</span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                                <!-- /.info-box -->
                            </div>
			{{-- [dashboard_link] --}}
                           
                        </div>	
				    </div>
                </div>
            </div>
        </div>			
		   
    </section>
</div><!--end #content-->


			
@endsection
@section('after-scripts')
	
@endsection
