<template>
    <div class="p-6">
      <div v-if="loading" class="text-center text-gray-500">Loading...</div>
      <div v-if="error" class="text-red-500">{{ error }}</div>
  
      <div v-if="course" class="bg-white p-4 shadow rounded">
        <h3 class="text-3xl font-semibold mb-4">{{ course.title }}</h3>
        <p class="text-gray-600 mb-4 text-ellipsis">{{ course.description }}</p>
        <video v-if="course.content" controls class="w-full">
          <source :src="`http://localhost:8000/api/${course.content}`" type="video/mp4" />
          Your browser does not support the video tag.
        </video>
      </div>
    </div>
  </template>
  
  <script>
  import api from "@/api";
  
  export default {
    name: "Course-Details",
    
    data() {
      return {
        course: null,
        loading: false,
        error: null,
      };
    },
    async mounted() {
      await this.fetchCourseDetails();
    },
    methods: {
      async fetchCourseDetails() {
        this.loading = true;
        this.error = null;
        try {
          const courseId = this.$route.params.id;
          const response = await api.get(`/course`, { params: { id: courseId } });
          this.course = response.data[0]; 
          console.log(this.course);
          
        } catch (err) {
          this.error = "Failed to fetch course details.";
          console.error(err);
        } finally {
          this.loading = false;
        }
      },
    },
  };
  </script>
  
  <style scoped>
  </style>
  