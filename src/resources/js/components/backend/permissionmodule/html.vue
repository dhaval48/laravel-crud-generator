<template>
<div>
    <div class="col-md-12">
        <form class="form" method="POST" :action='this.module.store_route' v-on:submit.prevent="onSubmit" @keydown="form.errors.clear($event.target.name)">

            <input type="hidden" name="id" :value="this.module.id" v-if="this.module.id != 0">

            <div :class='form.errors.has("name")?"form-group has-error":"form-group"'>
                <label>{{this.module.lang.name}}</label>
                <input type='text' name='name' class='form-control' v-model='form.name' :placeholder="'Enter ' + this.module.lang.name">
                <span class='help-block text-danger' 
                    v-if='form.errors.has("name")'
                    v-text='form.errors.get("name")'></span>
            </div>
                
            <div class="row">
            <!-- [GridVueElement-1] -->
            </div>

            <div class="clearfix">&nbsp;</div>
            
            <div class="card-actionbar">
                <div class="card-actionbar-row">
                    <button type="submit" class="btn btn-flat btn-primary pull-right" :disabled="form.errors.any()">{{this.module.common.save}}</button>
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
                 // [GRID_RESET]
                
                var response = {
                    label:name,
                    value:name,
                }

                this.$root.$emit('permission_modulesCreated', response);
                this.$parent.activity_init();

            }).catch(function(){});
        }
    },
    mounted() {
            
         // [DropdownSearch]
    }
}
</script>