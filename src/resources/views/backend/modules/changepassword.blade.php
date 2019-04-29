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
                        <a href="{{ route($data['list_route']) }}"> 
                            <i class="fa fa-long-arrow-left" aria-hidden="true"></i>
                        </a>&nbsp;&nbsp;
                        <h3 class="box-title">{{ $data['lang']['change_password'] }}</h3>
                        
                    </div>

                    <div class="box-body"> 
                        <changepassword-view :module="{{json_encode($data)}}"></changepassword-view>
                    </div>
                </div>
            </div>
        </div>            
    </section>
</div><!--end #content-->     
@endsection

@section('after-scripts')

<script type="text/javascript">
  $(document).ready(function() {
    
  });
</script>
@endsection