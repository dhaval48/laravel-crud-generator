<!-- BEGIN FORM MODAL MARKUP -->
<div class="modal fade in" id="[DROP_MODULE]Modal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h5 class="modal-title" id="formModalLabel">
                    {{ Lang::get('[DROP_MODULE].create_title') }}
                </h5>
            </div>
            <div class="modal-body">
            	<div class="row">
            		<div class="col-md-12">
            
						<[DROP_MODULE]-view :module="{{json_encode($data['[DROP_MODULE]_module'])}}"></[DROP_MODULE]-view>

					</div>
		        </div>
	        </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->