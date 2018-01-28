
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

// window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example', require('./components/Example.vue'));
//Vue.component('reply',require('./components/Reply.vue'));
Vue.component('flash',require('./components/Flash.vue'));
// Vue.component('replies',require('./components/Replies.vue'));
Vue.component('thread',require('./components/pages/Thread.vue'));
Vue.component('pagination',require('./components/Pagination.vue'));
Vue.component('nav-notifications',require('./components/Nav_notifications.vue'));
//Vue.component('profile-avatar',require('./components/pages/Profileavatar.vue'));
// Vue.config.debug = true;
// Vue.config.devtools = true;
window.vm = new Vue({
    el: '#app'
});




