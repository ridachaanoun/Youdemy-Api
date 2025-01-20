<template>
    <div>
      <h2 class="text-3xl font-bold text-gray-800 mb-4">Admin Statistics</h2>
  
      <div v-if="loading" class="text-center text-gray-500">Loading statistics...</div>
      <div v-if="error" class="text-red-500">{{ error }}</div>
  
      <div v-if="statistics" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-4 shadow rounded">
          <h3 class="text-xl font-semibold">Total Teachers</h3>
          <p class="text-2xl font-bold text-blue-500">{{ statistics.total_teachers }}</p>
        </div>
  
        <div class="bg-white p-4 shadow rounded">
          <h3 class="text-xl font-semibold">Total Students</h3>
          <p class="text-2xl font-bold text-blue-500">{{ statistics.total_students }}</p>
        </div>
  
        <div class="bg-white p-4 shadow rounded">
          <h3 class="text-xl font-semibold">Total Courses</h3>
          <p class="text-2xl font-bold text-blue-500">{{ statistics.total_courses }}</p>
        </div>
  
        <div class="bg-white p-4 shadow rounded">
          <h3 class="text-xl font-semibold">Total Enrollments</h3>
          <p class="text-2xl font-bold text-blue-500">{{ statistics.total_enrollments }}</p>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import api from "@/api";
  
  export default {
    name: "AdminStatistics",
    data() {
      return {
        statistics: null,  // Store the statistics data
        loading: false,
        error: null,
      };
    },
    mounted() {
      this.fetchStatistics();
    },
    methods: {
      async fetchStatistics() {
        this.loading = true;
        try {
          const response = await api.get("/admin/statistics");
          this.statistics = response.data;
        } catch (err) {
          this.error = "Failed to load statistics. Please try again.";
          console.error(err);
        } finally {
          this.loading = false;
        }
      },
    },
  };
  </script>
  
  <style scoped>
  /* Add any styles here if needed */
  </style>
  