<template>
<div>
  <div class="login-box-body">
    <p class="login-box-msg">Sign in to start your session</p>
      <form role="form" method="POST" action="/login" @submit.prevent="onSubmit">
        <div :class='form.errors.has("email")?"form-group has-feedback has-error":"form-group has-feedback"'>
          <input type="email" name="email" class="form-control" placeholder="Email" v-model="form.email">
          <span class="glyphicon glyphicon-envelope form-control-feedback"></span>

          <span id='email-error' class='help-block text-danger' 
            v-if='form.errors.has("email")'
            v-text='form.errors.get("email")'></span>
        </div>
        <div :class='form.errors.has("password")?"form-group has-feedback has-error":"form-group has-feedback"'>
          <input type="password" name="password" class="form-control" placeholder="Password" v-model="form.password">
          <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          <span id='password-error' class='help-block text-danger' 
            v-if='form.errors.has("password")'
            v-text='form.errors.get("password")'></span>            
        </div>
        <div class="row">
          <div class="col-xs-8">
            <p-check class="p-icon p-rotate p-bigger" color="primary" v-on:change="check">
                <i slot="extra" class="icon mdi mdi-check"></i>
                Remember Me
            </p-check>
          </div>
          <!-- /.col -->
          <div class="col-xs-4">
            <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

    <a href="/password/reset">I forgot my password</a><br>
  </div>
  <div class="row top10">
    <div class="text-center">
      Don't have an account? <a href="/register">Sign Up</a>
    </div>
  </div>
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
        check(event) {
          if(event){
            this.form.remember = true;
          } else {
            this.form.remember = false;
          }
        },

        onSubmit() {     
            this.form.post('/login').then(response => {
                if(response.meta.code == 200) {
                  window.location.href = '/home'
                }
            }).catch(error => {
                error.response.data.meta.message;
            });
        },

    },
    mounted() {

    }
}
</script>