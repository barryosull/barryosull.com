
window.Vue = require('vue');

import VueRouter from 'vue-router';

Vue.use(VueRouter);

Vue.component('nav-menu', require('./components/NavMenu.vue').default);
Vue.component('home', require('./components/Home.vue').default);
Vue.component('blog', require('./components/Blog.vue').default);
Vue.component('app', require('./components/App.vue').default);

const routes = [
    { path: '/', component: Vue.component('home') },
    { path: '/blog', component: Vue.component('blog') }
];

const router = new VueRouter({
    routes // short for `routes: routes`
});

const app = new Vue({
    router,
    el: '#app',
});
