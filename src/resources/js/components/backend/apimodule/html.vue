<template>
<div>
    <div class="col-md-12">
        <form class="form" method="POST" :action='this.module.store_route' @submit.prevent="onSubmit" @keydown="form.errors.clear($event.target.name)">

            <input type="hidden" name="id" :value="this.module.id" v-if="this.module.id != 0">
            
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">General</div>

                        <div class="card-body">
                            <div class="row">
                                <!-- <div class="col-md-2">{{this.module.lang.parent_module}}</div>
                                <div class='col-md-10'>
                                    <div :class='form.errors.has("parent_module")?"form-group has-error":"form-group"'>

                                        <select class="form-control select-form" ref='parent_module' name="parent_module" v-model="form.parent_module">
                                            <option value="">Select Permission Module</option>
                                            <option v-for="(value, key) in parent_module" :value='key'>{{value}}</option>
                                        </select>
                                        
                                    </div>
                                </div> -->

                                <div class="col-md-2">{{this.module.lang.main_module}}</div>
                                <div class='col-md-10'>
                                    <div :class='form.errors.has("main_module")?"form-group has-error":"form-group"'>

                                        <input type='text' name='main_module' class='form-control' v-model='form.main_module'>
                                        <span class='help-block text-danger' 
                                        v-if='form.errors.has("main_module")'
                                        v-text='form.errors.get("main_module")'></span>
                                    </div>
                                </div>

                                <div class="col-md-2">{{this.module.lang.is_model}}</div>
                                <div class='col-md-10'>
                                    <div :class='form.errors.has("is_model")?"form-group has-error":"form-group"'>
                                        <p-input type='checkbox' class='p-icon p-rotate p-bigger' color='primary' v-bind:true-value='1' v-bind:false-value='0' v-model='form.is_model'>
                                            <i slot='extra' class='icon mdi mdi-check'></i>
                                        </p-input>
                                        <span class='help-block text-danger' 
                                        v-if='form.errors.has("is_model")'
                                        v-text='form.errors.get("is_model")'></span>
                                    </div>
                                </div>

                                <div class="col-md-2">{{this.module.lang.is_public}}</div>
                                <div class='col-md-10'>
                                    <div :class='form.errors.has("is_public")?"form-group has-error":"form-group"'>
                                        <p-input type='checkbox' class='p-icon p-rotate p-bigger' color='primary' v-bind:true-value='1' v-bind:false-value='0' v-model='form.is_public'>
                                            <i slot='extra' class='icon mdi mdi-check'></i>
                                        </p-input>
                                        <span class='help-block text-danger' 
                                        v-if='form.errors.has("is_public")'
                                        v-text='form.errors.get("is_public")'></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix">&nbsp;</div>
                    <div class="card">
                        <div class="card-header bg-primary text-white">Database table settings</div>
                        <div class="card-body">

                            <div class="row">

                                <div class="col-md-2">{{this.module.lang.table_name}}</div>
                                <div class='col-md-10'>
                                    <div :class='form.errors.has("table_name")?"form-group has-error":"form-group"'>

                                        <input type='text' name='table_name' class='form-control' v-model='form.table_name'>
                                        <span class='help-block text-danger' 
                                        v-if='form.errors.has("table_name")'
                                        v-text='form.errors.get("table_name")'></span>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <legend>
                                        Table Fields
                                    </legend>                                       
                                </div>

                                <grid :module="this.module" :elementdata="this.module.api_tables" :elementrow="this.module.api_tables_row" :rowcount="this.module.api_tablesrow_count" ref="api_tables"></grid>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix">&nbsp;</div>
            <div class="card-actionbar">
                <div class="card-actionbar-row">
                    <button v-if="!is_save" type="submit" class="btn btn-flat btn-primary" :disabled="form.errors.any()">{{this.module.common.save}}</button>

                    <button v-else class="btn btn-primary" type="button" disabled>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <span>{{this.module.id != 0 ? 'Updating':'Saving'}}...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
</template>

<script>

export default {
    
    props:['formObj','module'],

    data(){
        return {
            form:this.formObj,
            parent_module : [],
            is_save:false,
            // [OptionsData]
        }
    },
    methods: {
        
        onSubmit() {
            this.is_save = true;
            this.form.post(this.module.store_route).then(response => {
            this.is_save = false;
            if(this.module.id == 0) {

                var grid = this.module.api_tables;
                
                for(var key in grid) {                        
                   this.$refs.api_tables.form[grid[key].name] = [""];
                }


                this.$refs.api_tables.rows = [];
                this.$refs.api_tables.rows = [0];
                this.$refs.api_tables.index = 0;

                
                this.$parent._data.form = new Form(this.module.fillable);
                this.form = this.$parent._data.form;
                this.$refs.api_tables.form  = this.module.fillable;

            }

            // [GRID_RESET]

                this.$root.$emit('api_modulesCreated', response);
                this.$parent.activity_init();

            }).catch(function(){});
        }
    },
    mounted() {

        if(this.module.parent_module_search) {
            axios.get(this.module.parent_module_search).then(data => {
                this.parent_module = data.data;
            });
        }
        // [DropdownSearch]
    }
}
</script>