<template>
    <div class="container mx-auto p-6">
      <h2 class="text-2xl font-semibold mb-4">Teacher Statistics</h2>
      
      <!-- Display the statistics -->
      <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between mb-4">
          <div class="text-gray-700 font-medium">Total Courses</div>
          <div class="text-blue-500">{{ totalCourses }}</div>
        </div>
        
        <div class="flex justify-between mb-4">
          <div class="text-gray-700 font-medium">Total Students</div>
          <div class="text-blue-500">{{ totalStudents }}</div>
        </div>
      </div>
      
      <!-- Show an error message if there's a problem fetching the data -->
      <p v-if="errorMessage" class="text-red-500 mt-2">{{ errorMessage }}</p>
    </div>
  </template>
  
  <script>
  import api from "@/api"; 
  
  export default {
    data() {
      return {
        totalCourses: 0,
        totalStudents: 0,
        errorMessage: "", 
      };
    },
    mounted() {
      this.fetchStatistics();
    },
    methods: {
        
      async fetchStatistics() {
        try {
          const response = await api.get("/teacher/statistics");
          const { total_courses, total_students } = response.data;
  
          // Set the data to variables
          this.totalCourses = total_courses;
          this.totalStudents = total_students;
        } catch (error) {
          // Handle any errors 
          this.errorMessage = "Error fetching statistics. Please try again.";
          console.error("Error fetching statistics:", error);
        }
      },
    },
  };
  </script>
  
  