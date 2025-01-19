import { createRouter, createWebHistory } from 'vue-router';
import Courses from '../components/Courses_List_vue.vue';
import Login from '../views/Login-page.vue';
import Register from '../views/register-page.vue';
import StudentDashboard from '../views/Student-Dashboard.vue';

const routes = [
    { path: "/", component: Courses }, // Show courses on the home page
    { path: "/courses", component: Courses },
    { path: '/login', name: 'Login', component: Login },
    { path: '/register', name: 'Register', component: Register },
    { path: '/student/dashboard', name: 'StudentDashboard', component: StudentDashboard, meta: { requiresAuth: true , requiresRole: ['Student'] }, // Protect route
    },
  ];
  
  const router = createRouter({
    history: createWebHistory(),
    routes,
  });
  
  import api from "@/api"; 


  router.beforeEach(async (to, from, next) => {
    const apiKey = document.cookie.includes('api_key');
  
    if (to.meta.requiresAuth && !apiKey) {
      return next('/login'); // Redirect to login if not authenticated
    }
  
    if (to.meta.requiresAuth) {
      try {
        // Fetch the user role 
        const response = await api.get('/user/role');
        const userRole = response.data.Role; 
  
        if (to.meta.requiresRole && !to.meta.requiresRole.includes(userRole)) {
          return next('/sssssssss');
        }
  
        next(); 
      } catch (error) {
        console.error('Error fetching user role:', error);
        return next('/login'); 
      }
    } else {
      next(); 
    }
  });

  export default router;