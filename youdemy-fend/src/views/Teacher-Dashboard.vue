<template>
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-4">Teacher Dashboard</h1>
  
      <!-- Loading or error state -->
      <div v-if="loading" class="text-center">Loading...</div>
      <div v-else-if="error" class="text-red-500">{{ error }}</div>
  
      <!-- Displaying the list of courses -->
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
            <div class="mt-4 flex gap-2">
              <button @click="openEditModal(course)" class="px-3 py-1 bg-yellow-500 text-white rounded-lg">Edit</button>
            </div>
          </div>
        </div>
        <p v-else class="text-gray-500">No courses found.</p>
      </div>
  
      <!-- Add Course Button -->
      <button @click="openModal" class="mt-6 px-4 py-2 bg-blue-500 text-white rounded-lg">Add New Course</button>
  
      <!-- Modal to add/edit a course -->
      <div v-if="isModalOpen" class="fixed inset-0 flex items-center justify-center bg-gray-600 bg-opacity-50" @click.self="closeModal">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96 relative">
          <!-- Close Button -->
          <button @click="closeModal" class="absolute top-2 right-2 text-gray-600 text-2xl font-bold">&times;</button>
  
          <h2 class="text-xl font-semibold mb-4">{{ isEditing ? "Edit Course" : "Add New Course" }}</h2>
          <form @submit.prevent="submitCourseForm">
            <!-- Course Title -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700">Course Title</label>
              <input type="text" v-model="newCourse.title" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required />
            </div>
  
            <!-- Course Description -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700">Course Description</label>
              <textarea v-model="newCourse.description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required></textarea>
            </div>
  
            <!-- Category -->
            <div v-if="categories.length" class="mb-4">
              <label class="block text-sm font-medium text-gray-700">Category</label>
              <select v-model="newCourse.category_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
              </select>
            </div>
  
            <!-- Tags -->
            <div v-if="tags.length" class="mb-4">
              <label class="block text-sm font-medium text-gray-700">Tags</label>
              <div class="grid grid-cols-2 gap-4">
                <label v-for="tag in tags" :key="tag.id" class="inline-flex items-center">
                  <input type="checkbox" :value="tag.id" v-model="newCourse.tags" class="form-checkbox h-4 w-4 text-blue-500">
                  <span class="ml-2">{{ tag.name }}</span>
                </label>
              </div>
            </div>
  
            <!-- Video Upload -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700">Video</label>
              <input type="file" @change="handleFileChange" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
            </div>
  
            <!-- Submit Button -->
            <div class="flex justify-end">
              <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg">
                {{ isEditing ? "Update Course" : "Save Course" }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import api from "@/api";
  
  export default {
    data() {
      return {
        courses: [],
        tags: [],
        categories: [],
        loading: true,
        error: "",
        isModalOpen: false,
        isEditing: false,
        newCourse: {
          id: null,
          title: "",
          description: "",
          category_id: null,
          tags: [],
          video: null,
        },
      };
    },
    async created() {
      try {
        const [coursesResponse, tagsResponse, categoryResponse] = await Promise.all([
          api.get("/teacher/courses"),
          api.get("/tag/get"),
          api.get("/category/list")
        ]);
        this.courses = coursesResponse.data;
        this.tags = tagsResponse.data;
        this.categories = categoryResponse.data;
      } catch (err) {
        this.error = "Failed to load courses, tags, or categories.";
      } finally {
        this.loading = false;
      }
    },
    methods: {
      openModal() {
        this.isModalOpen = true;
        this.isEditing = false;
        this.newCourse = { id: null, title: "", description: "", category_id: null, tags: [], video: null };
      },
      openEditModal(course) {
        this.isModalOpen = true;
        this.isEditing = true;
        this.newCourse = {
          id: course.id,
          title: course.title,
          description: course.description,
          category_id: course.category.id,
          tags: course.tags.map(tag => tag.id),
          video: null,
        };
      },
      closeModal() {
        this.isModalOpen = false;
        this.newCourse = { id: null, title: "", description: "", category_id: null, tags: [], video: null };
      },
      handleFileChange(event) {
        this.newCourse.video = event.target.files[0];
      },
      async submitCourseForm() {
        try {
          const formData = new FormData();
          formData.append("courseId", this.newCourse.id);
          formData.append("title", this.newCourse.title);
          formData.append("description", this.newCourse.description);
          formData.append("categoryId", this.newCourse.category_id);
          formData.append("tags", this.newCourse.tags.join(","));
          
          if (this.newCourse.video) {
            formData.append("video", this.newCourse.video);
          }
  
          if (this.isEditing) {
            await api.post("/teacher/course/edit", formData, {
              headers: { "Content-Type": "multipart/form-data" },
            });
            alert("Course updated successfully!");
          } else {
            await api.post("/teacher/course/add", formData, {
              headers: { "Content-Type": "multipart/form-data" },
            });
            alert("Course added successfully!");
          }
  
          this.closeModal();
          window.location.reload();
        } catch (err) {
          console.error("Error submitting course", err);
        }
      },
    },
  };
  </script>
  