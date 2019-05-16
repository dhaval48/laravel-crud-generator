<template>
<div>
    <div class="col-md-12">
        <form class="form" method="POST" :action='this.module.store_route' @submit.prevent="onSubmit" @keydown="form.errors.clear($event.target.name)">

            <input type="hidden" name="id" :value="this.module.id" v-if="this.module.id != 0">
            
    		<div class="row">
                <div class='col-sm-6'>
                    <div :class='form.errors.has("name")?"form-group has-error":"form-group"'>
                        <label for='name'>{{this.module.lang.name}}</label>
                        <input type='text' name='name' class='form-control' v-model='form.name'>
                        
                        <span id='name-error' class='help-block text-danger' 
                        v-if='form.errors.has("name")'
                        v-text='form.errors.get("name")'></span>
                    </div>
                </div>
                
                <div class='col-sm-6'>
                    <div :class='form.errors.has("email")?"form-group has-error":"form-group"'>
                        <label for='email'>{{this.module.lang.email}}</label>

                        <input type='text' name='email' class='form-control' v-model='form.email'>
                        <span id='email-error' class='help-block text-danger' 
                        v-if='form.errors.has("email")'
                        v-text='form.errors.get("email")'></span>
                    </div>
                </div>
    		</div>
            <template v-if="this.module.id == 0">
        		<div class="row">
                    <div class='col-sm-6'>
                        <div :class='form.errors.has("password")?"form-group has-error":"form-group"'>
                            <label for='password'>{{this.module.lang.password}}</label>

                            <input type='password' name='password' class='form-control' v-model='form.password'>
                            <span id='password-error' class='help-block text-danger' 
                            v-if='form.errors.has("password")'
                            v-text='form.errors.get("password")'></span>
                        </div>
                    </div>
                    
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="Confirmpassword">{{this.module.lang.confirm_password}}</label>

                            <input type="password" name="password_confirmation" class="form-control" v-model="form.password_confirmation">
                            <span id="Firstname1-error" class="help-block text-danger" v-if="form.errors.has('password_confirmation')" v-text="form.errors.get('password_confirmation')"></span>
                        </div>
                    </div>
        		</div>
            </template>
                

            <div class="row">
                <div class='col-sm-6'>
                    <div class="form-group">
                        <label for="assign_role">{{this.module.lang.assign_role}}</label>
                        
                        <select class="form-control select-form" ref='role_id' name="role_id" v-model="form.role_id">
                            <option value="">Select Role</option>
                            <option v-for="(value, key) in data" :value='key'>{{value}}</option>
                        </select> 

                    </div>
                </div>
            </div>
        
            <div class="card-actionbar">
                <div class="card-actionbar-row">
                    <button 
                        v-if="!is_save"
                        type="submit"
                        class="btn btn-flat btn-primary btn-loading-state" 
                        data-loading-text="<i class='fa fa-spinner fa-spin'></i> Saving..."
                        :disabled="form.errors.any()">{{this.module.common.save}}</button>

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
import moment from 'moment';

export default {
    
    props:['formObj','module'],
    
		//[GridComponent]
    data(){
        return {
            form:this.formObj,
            data:[],
            is_save:false
            
			//[OptionsData]
        }
    },
    methods: {
        onSubmit() {   
            this.is_save = true;  
            this.form.post(this.module.store_route).then(response => {
                this.is_save = false;
                this.$root.$emit('usersCreated', response);
                this.$parent.activity_init();
            }).catch(function(){});
        },
    },
    mounted() {
        this.form.date = moment().format("MM/DD/YYYY");
        
        $(document).ready( () => { 

            // $(document).on('change', '.select-form', event => {
            //     var input_db_name = $(event.target).attr('name');
            //     this.form[input_db_name] = event.target.value
            // });

        });
        
        if(this.module.role_search) {
            axios.get(this.module.role_search).then(data => {
                this.data = data.data;
            });
        }

        // this.$root.$on('userRoleCreated', (response) => {                
        //     this.data = [response];
        //     this.form.role_id = response;
        // })
		//[DropdownSearch]
    }
}
</script>