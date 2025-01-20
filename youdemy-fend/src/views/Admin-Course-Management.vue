<template>
    <div>
      <h2 class="text-3xl font-bold text-gray-800 mb-4">Course Management</h2>
  
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
          
          <!-- Button to add tags to course -->
          <button 
            @click="showTagModal(course.id)" 
            class="mt-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
          >
            Add Tags
          </button>
  
          <button 
            @click="deleteCourse(course.id)" 
            class="mt-2 px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
          >
            Delete
          </button>
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
  
      <!-- Add Tags Modal -->
      <div v-if="isTagModalVisible" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center z-50">
        <div class="bg-white p-6 rounded shadow-lg w-1/2">
          <h3 class="text-xl font-semibold mb-4">Assign Tags to Course</h3>
          <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Select Tags</label>
            <div class="space-y-2">
              <div v-for="tag in tags" :key="tag.id" class="flex items-center">
                <input 
                  type="checkbox" 
                  :value="tag.id" 
                  v-model="selectedTagIds" 
                  class="mr-2"
                />
                <label>{{ tag.name }}</label>
              </div>
            </div>
          </div>
          <div class="flex justify-end gap-2">
            <button 
              @click="closeTagModal"
              class="px-4 py-2 bg-gray-300 text-white rounded"
            >
              Cancel
            </button>
            <button 
              @click="assignTagsToCourse"
              class="px-4 py-2 bg-blue-500 text-white rounded"
            >
              Assign Tags
            </button>
          </div>
        </div>
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
        tags: [], 
        selectedTagIds: [],  
        loading: false,
        error: null,
        limit: 15,
        offset: 0,
        page: 1,
        searchQuery: "", 
        isTagModalVisible: false,  
        courseIdToAssignTags: null,  
      };
    },
    mounted() {
      this.fetchCourses();
      this.fetchTags();  // Fetch tags when the component is mounted
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
      async fetchTags() {
        try {
          const response = await api.get("tag/get");  // Adjust this URL if needed
          this.tags = response.data;
        } catch (err) {
          console.error("Failed to load tags:", err);
        }
      },
      async handleSearch() {
        if (!this.searchQuery) {
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
        if (this.searchQuery) return;
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
      async deleteCourse(courseId) {
        if (!confirm("Are you sure you want to delete this course?")) return;
  
        try {
          await api.post("/admin/courses/delete", { courseId });
          this.courses = this.courses.filter(course => course.id !== courseId);
          alert("Course deleted successfully!");
        } catch (err) {
          alert("Failed to delete course.");
        }
      },
      showTagModal(courseId) {
        this.courseIdToAssignTags = courseId;
        this.isTagModalVisible = true;
      },
      closeTagModal() {
        this.isTagModalVisible = false;
        this.selectedTagIds = [];
      },
      async assignTagsToCourse() {
        if (!this.selectedTagIds.length) {
          alert("Please select at least one tag.");
          return;
        }
  
        try {
          await api.post("/admin/courses/tags", {
            courseId: this.courseIdToAssignTags,
            tagIds: this.selectedTagIds,
          });
          alert("Tags assigned successfully!");
          this.closeTagModal();
        } catch (err) {
          alert("Failed to assign tags.");
        }
      },
    },
  };
  </script>
  