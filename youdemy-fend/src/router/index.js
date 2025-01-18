import { createRouter, createWebHistory } from 'vue-router';
import Courses from '../components/Courses_List_vue.vue';


const routes = [
    { path: "/", component: Courses }, // Show courses on the home page
    { path: "/courses", component: Courses },
  ];
  
  const router = createRouter({
    history: createWebHistory(),
    routes,
  });
  
  export default router;