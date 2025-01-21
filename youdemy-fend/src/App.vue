<template>
  <div class="min-h-screen bg-gray-100 text-gray-900">
    <nav class="bg-blue-600 text-white p-4">
      <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-2xl font-bold">Youdemy</h1>
        <ul class="flex space-x-4">
          <li><router-link to="/" class="hover:underline">Home</router-link></li>
          <li><router-link to="/courses" class="hover:underline">Courses</router-link></li>
          <li v-if="!isLoggedIn"><router-link to="/login" class="hover:underline">Login</router-link></li>
          <li v-if="isLoggedIn && isStudent">
            <router-link to="/student/dashboard" class="hover:underline">Student Dashboard</router-link>
          </li>
          <li v-if="isLoggedIn && isTeacher">
            <router-link to="/teacher/dashboard" class="hover:underline" :class="isActive?'text-green-500':'text-red-500'">Teacher Dashboard</router-link>
          </li>
          <li v-if="isLoggedIn && isAdmin">
            <router-link to="/Admin/TagManagement" class="hover:underline">Tag Management</router-link>
          </li>
          <li v-if="isLoggedIn && isAdmin">
            <router-link to="/Admin/CategoryManagement" class="hover:underline">Category Management</router-link>
          </li>
          <li v-if="isLoggedIn && isAdmin">
            <router-link to="/Admin/CourseManagement" class="hover:underline">Course Management</router-link>
          </li>
          <li v-if="isLoggedIn && isAdmin">
            <router-link to="/Admin/Statistics" class="hover:underline">tatistics</router-link>
          </li>
          <li v-if="isLoggedIn && isAdmin">
            <router-link to="/Admin/StudentManagemen" class="hover:underline">Students</router-link>
          </li>
          <li v-if="isLoggedIn && isAdmin">
            <router-link to="/Admin/TeacherManagemen" class="hover:underline">Teacher</router-link>
          </li>
          <li v-if="isLoggedIn && isTeacher">
            <router-link to="/teacher/Statistics" class="hover:underline" :class="isActive?'text-green-500':'text-red-500'">Teacher Statistics</router-link>
          </li>
          <li v-if="isLoggedIn">
            <button @click="logout" class="bg-red-500 px-4 py-2 rounded hover:bg-red-600">Logout</button>
          </li>
        </ul>
      </div>
    </nav>

    <div class="container mx-auto p-6">
      <router-view></router-view>
    </div>
  </div>
</template>

<script>
import api from "@/api"; // Import the api instance

export default {
  name: "App",
  data() {
    return {
      isLoggedIn: false,
      isStudent: false, // Track if the user is a student
      isTeacher:false,
      isAdmin:false,
      isActive:false,
    };
  },
  mounted() {
    this.checkLoginStatus();
  },
  methods: {
    // Method to check if api_key exists in cookie
    checkLoginStatus() {
      const cookies = document.cookie.split(';');
      const apiKey = cookies.find(cookie => cookie.trim().startsWith('api_key='));
      this.isLoggedIn = !!apiKey; // If api_key exists => true

      // If logged in, fetch user role
      if (this.isLoggedIn) {
        this.checkUserRole();
      }
    },

    async checkUserRole() {
      try {
        const response = await api.get('/user/role');
        const userRole = response.data.Role;
        const userStatus = response.data.Status;
        
        this.isStudent = userRole === 'Student';
        this.isTeacher = userRole === 'Teacher';
        this.isAdmin = userRole === 'Admin';
        this.isActive = userStatus === "active"
        
      } catch (error) {
        console.error('Error fetching user role:', error);
      }
    },
    logout() {
      document.cookie = "api_key=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
      this.isLoggedIn = false;
      this.isStudent = false;
      this.isTeacher = false;
      this.isAdmin = false;
      this.$router.push("/login"); // Redirect to login page
    }
  }
};
</script>

<style>
</style>
