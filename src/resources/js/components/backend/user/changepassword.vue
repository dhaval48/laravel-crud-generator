<template>
<div>
  <form id="form-change-password" role="form" method="POST" :action='this.module.store_route' @submit.prevent="onSubmit" novalidate class="form-horizontal">
        <div class="col-md-9">             
            <label for="current-password" class="col-sm-4 control-label">Current Password</label>
            <div class="col-sm-8">
                <div :class='form.errors.has("current_password")?"form-group has-error":"form-group"'>
                    <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Password" v-model="form.current_password">
                    <span id='current_password-error' class='help-block' text-danger 
                        v-if='form.errors.has("current_password")'
                        v-text='form.errors.get("current_password")'></span>
                </div>
            </div>
            <label for="password" class="col-sm-4 control-label">New Password</label>
            <div class="col-sm-8">
                <div :class='form.errors.has("password")?"form-group has-error":"form-group"'>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" v-model="form.password">
                    <span id='password-error' class='help-block text-danger' 
                        v-if='form.errors.has("password")'
                        v-text='form.errors.get("password")'></span>
                </div>
            </div>
            <label for="password_confirmation" class="col-sm-4 control-label">Re-enter Password</label>
            <div class="col-sm-8">
                <div :class='form.errors.has("password_confirmation")?"form-group has-error":"form-group"'>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Re-enter Password"  v-model="form.password_confirmation">
                    <span id='password_confirmation-error' class='help-block text-danger' 
                        v-if='form.errors.has("password_confirmation")'
                        v-text='form.errors.get("password_confirmation")'></span>
                </div>
            </div>
            
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-6">
                    <button type="submit" class="btn btn-danger">Submit</button>
                </div>
            </div>
        </div>

    </form>
</div>
</template>

<script>

export default {
    
    props:['module'],
    
    data(){
        return {
            form:new Form(this.module.fillable),
        }
    },
    methods: {

        onSubmit() {     
            this.form.post(this.module.store_route).then(response => {
                // if(response.meta.code == 200) {
                //   window.location.href = '/logout'
                // }
            }).catch(error => {
                error.response.data.meta.message;
            });
        },

    },
    mounted() {

    }
}
</script>