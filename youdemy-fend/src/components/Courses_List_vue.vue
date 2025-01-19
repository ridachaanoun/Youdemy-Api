<template>
  <div>
    <h2 class="text-3xl font-bold text-gray-800 mb-4">Course Catalog</h2>

    <!-- Search Input -->
    <div class="mb-4">
      <input
        type="text"
        v-model="searchQuery"
        @input="handleSearch"
        placeholder="Search for courses..."
        class="border p-2 w-full rounded"
      />
    </div>

    <div v-if="loading" class="text-center text-gray-500">Loading courses...</div>
    <div v-if="error" class="text-red-500">{{ error }}</div>

    <div v-if="courses.length" class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div v-for="course in courses" :key="course.id" class="bg-white p-4 shadow rounded">
        <h3 class="text-xl font-semibold">{{ course.title }}</h3>
        <p class="text-gray-600 truncate h-15">{{ course.description }}</p>
      </div>
    </div>

    <div v-if="!loading && courses.length === 0" class="text-center text-gray-500">
      No courses available.
    </div>

    <!-- Pagination Controls (Hidden when searching) -->
    <div v-if="!searchQuery" class="flex justify-center items-center mt-6 space-x-4">
      <button 
        @click="prevPage" 
        :disabled="offset === 0" 
        class="px-4 py-2 bg-gray-300 rounded disabled:opacity-50"
      >
        Previous
      </button>

      <span class="text-gray-700">Page {{ page }}</span>

      <button 
        @click="nextPage" 
        :disabled="courses.length < limit" 
        class="px-4 py-2 bg-blue-500 text-white rounded disabled:opacity-50"
      >
        Next
      </button>
    </div>
  </div>
</template>

<script>
import api from "@/api";

export default {
  name: "CoursesList",
  data() {
    return {
      courses: [],
      loading: false,
      error: null,
      limit: 15,
      offset: 0,
      page: 1,
      searchQuery: "", // Store search keyword
    };
  },
  mounted() {
    this.fetchCourses();
  },
  methods: {
    async fetchCourses() {
      this.loading = true;
      try {
        const response = await api.get(`/user/courses?limit=${this.limit}&offset=${this.offset}`);
        this.courses = response.data;
      } catch (err) {
        this.error = "Failed to load courses. Please try again.";
        console.error(err);
      } finally {
        this.loading = false;
      }
    },
    async handleSearch() {
      if (!this.searchQuery) {
        // If search is empty, reload paginated courses
        this.fetchCourses();
        return;
      }

      this.loading = true;
      try {
        const response = await api.get(`/user/search?keyword=${this.searchQuery}`);
        this.courses = response.data;
      } catch (err) {
        this.error = "Search failed. Please try again.";
        console.error(err);
      } finally {
        this.loading = false;
      }
    },
    nextPage() {
      if (this.searchQuery) return; // Disable pagination when searching
      this.offset += this.limit;
      this.page += 1;
      this.fetchCourses();
    },
    prevPage() {
      if (this.searchQuery) return;
      if (this.offset > 0) {
        this.offset -= this.limit;
        this.page -= 1;
        this.fetchCourses();
      }
    },
  },
};
</script>
