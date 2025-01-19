<template>
    <div class="p-6">
      <h2 class="text-3xl font-bold text-gray-800 mb-6">Student Dashboard</h2>
  
            <!-- Search Bar -->
            <div class="mb-4">
        <input
          type="text"
          v-model="searchQuery"
          @input="searchCourses"
          placeholder="Search courses..."
          class="border p-2 w-full rounded"
        />
      </div>

      <!-- My Courses Section -->
      <h3 class="text-2xl font-semibold mb-4">My Courses</h3>
      <div v-if="loading" class="text-center text-gray-500">Loading...</div>
      <div v-if="error" class="text-red-500">{{ error }}</div>
  
      <div v-if="enrolledCourses.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="course in enrolledCourses" :key="course.id" class="bg-white p-4 shadow rounded">
          <h3 class="text-xl font-semibold">{{ course.title }}</h3>
          <p class="text-gray-600 truncate">{{ course.description }}</p>
          <router-link :to="'/course/' + course.id" class="text-blue-500 mt-2">View Details</router-link>
        </div>
      </div>
  
      <div v-if="!loading && enrolledCourses.length === 0" class="text-gray-500">
        You are not enrolled in any courses.
      </div>
  
      <!-- Available Courses Section -->
      <h3 class="text-2xl font-semibold mt-8 mb-4">Available Courses</h3>
      <div v-if="searchQuery && searchResults.length === 0" class="text-gray-500">No courses found.</div>
  
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="course in (searchQuery ? searchResults : availableCourses)" :key="course.id" class="bg-white p-4 shadow rounded">
          <h3 class="text-xl font-semibold">{{ course.title }}</h3>
          <p class="text-gray-600 truncate">{{ course.description }}</p>
          
          <!-- Check if course is already enrolled and hide the button if it is -->
          <button
            v-if="!isEnrolled(course.id)"
            @click="enrollInCourse(course.id)"
            class="bg-blue-500 text-white px-4 py-2 rounded mt-2"
          >
            Enroll
          </button>
        </div>
      </div>
    </div>
  </template>
  
  
  <script>
  import api from "@/api";
  
  export default {
    name: "StudentDashboard",
    data() {
      return {
        enrolledCourses: [],
        availableCourses: [],
        searchResults: [],
        searchQuery: "",
        loading: false,
        error: null,
      };
    },
    async mounted() {
      await this.fetchStudentDetails();
      await this.fetchAvailableCourses();
    },
    methods: {
      async fetchStudentDetails() {
        this.loading = true;
        this.error = null;
        try {
          const response = await api.get("/student/details");
          this.enrolledCourses = response.data.enrolled_courses;
        } catch (err) {
          this.error = "Failed to fetch student details.";
          console.error(err);
        } finally {
          this.loading = false;
        }
      },
      async fetchAvailableCourses() {
        this.loading = true;
        try {
          const response = await api.get("/user/courses");
          this.availableCourses = response.data;
        } catch (err) {
          console.error(err);
        } finally {
          this.loading = false;
        }
      },
      async enrollInCourse(courseId) {
        try {
          const response = await api.post("/student/enroll", { course_id: courseId });
  
          alert(response.data.message);
          this.fetchStudentDetails(); // Refresh enrolled courses
        } catch (err) {
          console.error("Enrollment failed", err);
        }
      },
      async searchCourses() {
        if (!this.searchQuery) {
          this.searchResults = [];
          return;
        }
        try {
          const response = await api.get(`/user/search?keyword=${this.searchQuery}`);
          this.searchResults = response.data;
        } catch (err) {
          console.error("Search failed", err);
        }
      },
      // Method to check if the course is already enrolled
      isEnrolled(courseId) {
        return this.enrolledCourses.some(course => course.id === courseId);
      }
    },
  };
  </script>
  