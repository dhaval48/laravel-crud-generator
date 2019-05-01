<template>
<div>
    <div class="col-md-12">
        <form class="form" method="POST" :action='this.module.store_route' @submit.prevent="onSubmit" @keydown="form.errors.clear($event.target.name)">

            <input type="hidden" name="id" :value="this.module.id" v-if="this.module.id != 0">
            
			<div class="row">
                <div class='col-sm-6'>
                    <div :class='form.errors.has("enable_registration")?"form-group has-error":"form-group"'>
                        <p-input type='checkbox' name='enable_registration' class='p-icon p-rotate p-bigger' color='primary' v-bind:true-value='1' v-bind:false-value='0' v-model='form.enable_registration'>
                            <i slot="extra" class="icon fa fa-check"></i>
                            {{this.module.lang.enable_registration}}
                        </p-input>
                        <span class='help-block text-danger' 
                        v-if='form.errors.has("enable_registration")'
                        v-text='form.errors.get("enable_registration")'></span>
                    </div>
                </div>
                
			</div>

            <div class="row">
            <!-- [GridVueElement-1] -->
            </div>

            <!-- <div class="row">
                <div class="col-sm-12">
                    <file_upload ref="file_upload" :module='this.module'></file_upload>
                </div>
            </div> -->
            
            <div class="card-actionbar">
                <div class="card-actionbar-row">
                    <button type="submit" class="btn btn-flat btn-primary" :disabled="form.errors.any()">{{this.module.common.save}}</button>
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
         // [DropdownFunction]
        onSubmit() {            
            var name = this.form.name;
            this.form.post(this.module.store_route).then(response => {
                this.$refs.file_upload.submitFiles(this.module.dir, response.data.id);
                 // [GRID_RESET]
                
                var response = {
                    label:name,
                    value:response.data.id
                }

                if(this.module.id == 0) {
                    this.$refs.file_upload.files = [];
                    this.$refs.file_upload.name = [];
                } else {
                     this.$refs.file_upload.init();
                }
                this.$root.$emit('settingsCreated', response);
                this.$parent.activity_init();

            }).catch(function(){});
        }
    },
    mounted() {
            
         // [DropdownSearch]
    }
}
</script>