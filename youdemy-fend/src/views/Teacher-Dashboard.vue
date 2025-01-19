<template>
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-4">Teacher Dashboard</h1>
      <div v-if="loading" class="text-center">Loading...</div>
      <div v-else-if="error" class="text-red-500">{{ error }}</div>
      <div v-else>
        <div v-if="courses.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div v-for="course in courses" :key="course.id" class="p-4 border rounded-lg shadow-md bg-white">
            <h2 class="text-xl font-semibold">{{ course.title }}</h2>
            <p class="text-gray-600">{{ course.description }}</p>
            <p class="text-sm text-gray-500">Category: {{ course.category.name }}</p>
            <p class="text-sm text-gray-500">Students Enrolled: {{ course.students }}</p>
            <div class="mt-2">
              <span v-for="tag in course.tags" :key="tag.id" class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1">
                {{ tag.name }}
              </span>
            </div>
          </div>
        </div>
        <p v-else class="text-gray-500">No courses found.</p>
      </div>
    </div>
  </template>
  
  <script>
  import api from "@/api";
  export default {
    data() {
      return {
        courses: [],
        loading: true,
        error: ""
      };
    },
    async created() {
      try {
        const response = await api.get("/teacher/courses");
        this.courses = response.data;
      } catch (err) {
        this.error = "Failed to load courses.";
      } finally {
        this.loading = false;
      }
    }
  };
  </script>
  

  