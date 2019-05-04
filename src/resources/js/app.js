
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

require('./component');

Vue.component('paginate-links',require('./components/common/paginate_link.vue').default);
Vue.component('activity',require('./components/common/activity.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */


import Toasted from 'vue-toasted';
Vue.use(Toasted)

import VueSweetalert2 from 'vue-sweetalert2';
Vue.use(VueSweetalert2);

import filter from './components/core/filter';
Vue.use(filter);

import Datepicker from 'vuejs-datepicker';
Vue.component('Datepicker', Datepicker)

import activity from './components/common/activity.vue';
Vue.component('activity', activity)

import grid from './components/common/grid.vue';
Vue.component('grid', grid)

import auto_grid from './components/common/auto_grid.vue';
Vue.component('auto_grid', auto_grid)

import file_upload from './components/common/file_upload.vue';
Vue.component('file_upload', file_upload)

import PrettyCheckbox from 'pretty-checkbox-vue'; 
Vue.use(PrettyCheckbox);

const app = new Vue({
    el: '#app'
});
