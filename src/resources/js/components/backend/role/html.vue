<template>
<div>
    <div class="col-md-12">
        <form class="form" method="POST" :action='this.module.store_route' @submit.prevent="onSubmit" @keydown="form.errors.clear($event.target.name)">

            <input type="hidden" name="id" :value="this.module.id" v-if="this.module.id != 0">
            
            <div class="row">
                <div class='col-sm-6'>
                    <div :class='form.errors.has("name")?"form-group has-error":"form-group"'>
                        <label for='name'> {{this.module.lang.name}} </label>
                        
                        <input type='text' name='name' class='form-control' id='name' v-model='form.name'>
                        <span id='name-error' class='help-block text-danger' 
                        v-if='form.errors.has("name")'
                        v-text='form.errors.get("name")'></span>
                    </div>
                </div>
                
                <div class='col-sm-6'>
                    <div :class='form.errors.has("description")?"form-group has-error":"form-group"'>
                        <label for='description'> {{this.module.lang.description}} </label>

                        <input type='text' name='description' class='form-control' id='description' v-model='form.description'>
                        <span id='description-error' class='help-block text-danger' 
                        v-if='form.errors.has("description")'
                        v-text='form.errors.get("description")'></span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12" v-for="(group, k) in groups">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title"> 
                                <p-check class="p-icon p-jelly p-bigger" color="danger-o"
                                name="module_id" 
                                :value="group.id"
                                @click.native="check(k, $event)"
                                v-model="form.module_id">
                                    <i slot="extra" class="icon mdi mdi-check-all"></i>
                                    
                                    {{group.name}} 
                                    
                                </p-check>           
                            </h5>
                            <!-- <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                            </div> -->
                        </div>
                        <div class="card-body">
                            <template v-if="module_group.name != 'gridmodule'" v-for="(module_group,key) in group.module_groups">
                            
                                <div class="permission_group_div">

                                    <div class="module">
                                        <div class="row">
                                            <div class="col-md-12">
                                                
                                                <p-check class="p-icon p-rotate p-bigger" color="primary"
                                                :value="module_group.id"
                                                @click.native="checkGroup(k, key, $event)" 
                                                v-model="form.module_group_id">
                                                    <i slot="extra" class="icon mdi mdi-check"></i>
                                                    <b>{{module_group.display_name}} Module</b>
                                                </p-check>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4" v-for="(permission,index) in module_group.permissions">
                                            &nbsp;&nbsp;&nbsp;&nbsp;<p-check class="p-icon p-rotate" color="default"
                                            :value="permission.id"
                                            v-model='form.permission_id'
                                            @click.native="checkPermission(k, key, index, $event)">
                                                <i slot="extra" class="icon mdi mdi-check"></i>
                                                {{permission.display_name}}
                                            </p-check>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                <div class="clearfix">&nbsp;</div>
                </div>

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <template v-for="(module_group,key) in this.module.module_extra_list">
                            
                                <div class="permission_group_div">

                                    <div class="module">
                                        <div class="row">
                                            <div class="col-md-12">
                                                
                                                <p-check class="p-icon p-rotate p-bigger" color="primary"
                                                :value="module_group.id"
                                                @click.native="checkGroupExtra(key, $event)" 
                                                v-model="form.module_group_id">
                                                    <i slot="extra" class="icon mdi mdi-check"></i>
                                                    <b>{{module_group.display_name}} Module</b>
                                                </p-check>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4" v-for="(permission,index) in module_group.permissions">
                                            &nbsp;&nbsp;&nbsp;&nbsp;<p-check class="p-icon p-rotate" color="default"
                                            :value="permission.id"
                                            v-model='form.permission_id'>
                                                <i slot="extra" class="icon mdi mdi-check"></i>
                                                {{permission.display_name}}
                                            </p-check>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="clearfix">&nbsp;</div>
            </div>


            <div class="card-actionbar">
                <div class="card-actionbar-row">
                    
                    <button 
                        v-if="!is_save"
                        type="submit"
                        class="btn btn-flat btn-primary btn-loading-state" 
                        data-loading-text="<i class='fa fa-spinner fa-spin'></i> Saving..."
                        :disabled="form.errors.any()">{{this.module.common.save}}
                    </button>

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

<style type="text/css">
    .permission_group_div{
        padding-bottom: 10px;
        border: 1px solid aliceblue;
        padding: 10px;
        margin-bottom: 10px;
    }
    .module {
        margin-bottom: 5px;
    }
    
</style>

<script>
import moment from 'moment';

export default {
    
    props:['formObj','module','user'],
    
        //[GridComponent]
    data(){
        return {
            form:this.formObj,
            groups:this.module.module_list,
            is_save:false,

            //[OptionsData]
        }
    },
    methods: {

        check: function(k, event) {
            if(event.target.checked){
                for(let module_group of this.groups[k].module_groups){
                    this.form.module_group_id.push(module_group.id);
                    for(let permission of module_group.permissions){
                        this.form.permission_id.push(permission.id);
                    }
                }
            } else {
                for(let module_group of this.groups[k].module_groups){
                    $.each(this.form.module_group_id, (key, value) => {
                        if(this.form.module_group_id[key] == module_group.id) {
                            this.form.module_group_id.splice(key, 1);
                            
                            for(let permission of module_group.permissions){
                                $.each(this.form.permission_id, (index, val) => {
                                    if(this.form.permission_id[index] == permission.id) {
                                        this.form.permission_id.splice(index, 1);
                                    }
                                });
                            }
                        }
                    });
                }
            }
        },

        checkGroup: function(k, key, event) {

            if(event.target.checked){
                this.form.module_group_id.push(this.groups[k].module_groups[key].id);
                for(let permission of this.groups[k].module_groups[key].permissions){
                    this.form.permission_id.push(permission.id);
                }
            } else {
                $.each(this.form.module_id, (i, value) => {
                    if(this.form.module_id[i] == this.groups[k].id) {
                        this.form.module_id.splice(i, 1);
                    }
                });
                
                $.each(this.form.module_group_id, (i, value) => {
                    if(this.form.module_group_id[i] == this.groups[k].module_groups[key].id) {
                        this.form.module_group_id.splice(i, 1);
                        
                        for(let permission of this.groups[k].module_groups[key].permissions){
                            $.each(this.form.permission_id, (index, val) => {
                                if(this.form.permission_id[index] == permission.id) {
                                    this.form.permission_id.splice(index, 1);
                                }
                            });
                        }
                    }
                });
            }
        },

        checkGroupExtra: function(key, event) {

            if(event.target.checked){
                this.form.module_group_id.push(this.module.module_extra_list[key].id);
                for(let permission of this.module.module_extra_list[key].permissions){
                    this.form.permission_id.push(permission.id);
                }
            } else {
                
                $.each(this.form.module_group_id, (i, value) => {
                    if(this.form.module_group_id[i] == this.module.module_extra_list[key].id) {
                        this.form.module_group_id.splice(i, 1);
                        
                        for(let permission of this.module.module_extra_list[key].permissions){
                            $.each(this.form.permission_id, (index, val) => {
                                if(this.form.permission_id[index] == permission.id) {
                                    this.form.permission_id.splice(index, 1);
                                }
                            });
                        }
                    }
                });
            }
        },

        checkPermission: function(k, key, index, event) {
            // if(event.target.checked){
            //     this.form.permission_id.push(this.groups[k].module_groups[key].permission[index].id);
            // } else {
            //     this.form.permission_id.splice(index, 1);
            // }
        },

        onSubmit() {            
            var name = this.form.name;
            this.is_save = true;
            this.form.post(this.module.store_route).then(response => {
                this.is_save = false;
                var response = {
                    label:name,
                    value:response.data.id
                }
                this.$root.$emit('userRoleCreated', response);
                this.$parent.activity_init();
                
            }).catch(function(){});
        }
    },
    mounted() {
        
        //[DropdownSearch]
    }
}
</script>