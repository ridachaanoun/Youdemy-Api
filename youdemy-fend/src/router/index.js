import { createRouter, createWebHistory } from 'vue-router';
import Courses from '../components/Courses_List_vue.vue';
import Login from '../views/Login-page.vue';
import Register from '../views/register-page.vue';


const routes = [
    { path: "/", component: Courses }, // Show courses on the home page
    { path: "/courses", component: Courses },
    { path: '/login', name: 'Login', component: Login },
    { path: '/register', name: 'Register', component: Register },
  ];
  
  const router = createRouter({
    history: createWebHistory(),
    routes,
  });
  
  export default router;