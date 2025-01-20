import { createRouter, createWebHistory } from 'vue-router';
import Courses from '../components/Courses_List_vue.vue';
import Login from '../views/Login-page.vue';
import Register from '../views/register-page.vue';
import StudentDashboard from '../views/Student-Dashboard.vue';
import CourseDetails from '../components/Course-Details.vue';
import TeacherDashboard from '../views/Teacher-Dashboard.vue';
import AdminTagManagement from '../views/Admin-Tag-Management.vue';
import AdminCategoryManagement from '../views/Admin-Category-Management.vue';
import AdminCourseManagement from '../views/Admin-Course-Management.vue';
import AdminStatistics from '../views/AdminStatistics.vue';
import AdminStudentManagement from '../views/Admin-Student-Management.vue';

const routes = [
    { path: "/", component: Courses }, // Show courses on the home page
    { path: "/courses", component: Courses },
    { path: '/login', name: 'Login', component: Login },
    { path: '/register', name: 'Register', component: Register },
    { path: '/student/dashboard', name: 'StudentDashboard', component: StudentDashboard, meta: { requiresAuth: true , requiresRole: ['Student'] },},
    { path: '/course/:id', name: 'CourseDetails', component: CourseDetails, meta: { requiresAuth: true, requiresEnrollment: true } },
    { path: "/teacher/dashboard", name: "TeacherDashboard", component: TeacherDashboard, meta: { requiresAuth: true, requiresRole: ['Teacher'] } },
    { path: "/Admin/TagManagement", name: "TagManagement", component: AdminTagManagement, meta: { requiresAuth: true, requiresRole: ['Admin'] } },
    { path: "/Admin/CategoryManagement", name: "CategoryManagement", component: AdminCategoryManagement, meta: { requiresAuth: true, requiresRole: ['Admin'] } },
    { path: "/Admin/CourseManagement", name: "CourseManagement", component: AdminCourseManagement, meta: { requiresAuth: true, requiresRole: ['Admin'] } },
    { path: "/Admin/Statistics", name: "Statistics", component: AdminStatistics, meta: { requiresAuth: true, requiresRole: ['Admin'] } },
    { path: "/Admin/StudentManagemen", name: "StudentManagemen", component: AdminStudentManagement, meta: { requiresAuth: true, requiresRole: ['Admin'] } },
  ];
  
  const router = createRouter({
    history: createWebHistory(),
    routes,
  });
  
  import api from "@/api";
  
  router.beforeEach(async (to, from, next) => {
      const apiKey = document.cookie.includes('api_key');
  
      if (to.meta.requiresAuth && !apiKey) {
          return next('/login'); 
      }
  
      try {
          // Fetch the user role
          const response = await api.get('/user/role');
          const userRole = response.data.Role;
  
          // Restrict routes based on roles
          if (to.meta.requiresRole && !to.meta.requiresRole.includes(userRole)) {
              return next('/'); 
          }
  
          // Check if student is enrolled in the requested course
          if (to.meta.requiresEnrollment && userRole === 'Student') {
              const studentResponse = await api.get('/student/details');
              const enrolledCourses = studentResponse.data.enrolled_courses.map(course => course.id);
  
              const courseId = parseInt(to.params.id); 
              if (!enrolledCourses.includes(courseId)) {
                  return next('/'); 
              }
          }
  
          next();
      } catch (error) {
          console.error('Error in navigation guard:', error);
          return next('/login'); 
      }
  });
  
  export default router;
  