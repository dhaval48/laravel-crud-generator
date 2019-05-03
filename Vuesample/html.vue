<template>
<div>
    <div class="col-md-12">
        <form class="form" method="POST" :action='this.module.store_route' @submit.prevent="onSubmit" @keydown="form.errors.clear($event.target.name)">

            <input type="hidden" name="id" :value="this.module.id" v-if="this.module.id != 0">
            <div class="row">
                [FORM_FIELDS]
            </div>
            <div class="row">
            <!-- [GridVueElement-1] -->
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <file_upload ref="file_upload" :module='this.module'></file_upload>
                </div>
            </div>
            
            <div class="clearfix">&nbsp;</div>
            
            <div class="card-actionbar">
                <div class="card-actionbar-row">
                    <button type="submit" class="btn btn-flat btn-primary ink-reaction pull-right" :disabled="form.errors.any()">{{this.module.common.save}}</button>
                </div>
            </div>
        </form>
    </div>
</div>
</template>

<script>
import moment from 'moment';

export default {
    
    props:['formObj','module'],

    data(){
        return {
            form:this.formObj,
            // [OptionsData]
        }
    },
    methods: {
        onSubmit() {            
            this.form.post(this.module.store_route).then(response => {
                this.$refs.file_upload.submitFiles(this.module.dir, response.data.id);
                
                // [GRID_RESET]
                if(this.module.id == 0) {
                    this.$refs.file_upload.files = [];
                    this.$refs.file_upload.name = [];
                } else {
                    // // this.$refs.file_upload.init();
                }
                this.$root.$emit('[TNAME]Created', response);
                this.$parent.activity_init();

            }).catch(function(){});
        }
    },
    mounted() {
            
        // [DropdownSearch]
    }
}
</script>