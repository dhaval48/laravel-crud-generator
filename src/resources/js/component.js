Vue.component('login',require('./components/auth/login.vue').default);
Vue.component('users-view',require('./components/backend/user/view.vue').default);
Vue.component('changepassword-view',require('./components/backend/user/changepassword.vue').default);

Vue.component('roles-view',require('./components/backend/role/view.vue').default);

Vue.component('permission_modules-view',require('./components/backend/permissionmodule/view.vue').default);

Vue.component('form_modules-view',require('./components/backend/formmodule/view.vue').default);

Vue.component('grid_modules-view',require('./components/backend/gridmodule/view.vue').default);

Vue.component('api_modules-view',require('./components/backend/apimodule/view.vue').default);

Vue.component('language_translets-view',require('./components/backend/languagetranslet/view.vue').default);
// [VueComponent]