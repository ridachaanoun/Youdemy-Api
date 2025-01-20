<template>
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-4">Student Management</h1>
  
      <!-- Loading or Error State -->
      <div v-if="loading" class="text-center">Loading...</div>
      <div v-else-if="error" class="text-red-500">{{ error }}</div>
  
      <!-- Student List -->
      <div v-else>
        <div v-if="students.length" class="bg-white shadow-md rounded-lg p-4">
          <table class="w-full border-collapse">
            <thead>
              <tr class="bg-gray-100">
                <th class="border p-2 text-left">ID</th>
                <th class="border p-2 text-left">Name</th>
                <th class="border p-2 text-left">Email</th>
                <th class="border p-2 text-left">Status</th>
                <th class="border p-2 text-left">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="student in students" :key="student.id" class="border-b">
                <td class="border p-2">{{ student.id }}</td>
                <td class="border p-2">{{ student.name }}</td>
                <td class="border p-2">{{ student.email }}</td>
                <td class="border p-2">{{ student.status }}</td>
                <td class="border p-2">
                  <button 
                    @click="deleteUser(student.id)" 
                    class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                    Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <p v-else class="text-gray-500">No students found.</p>
      </div>
    </div>
  </template>
  
  <script>
  import api from "@/api"; // Adjust the path if necessary
  
  export default {
    data() {
      return {
        students: [],
        loading: true,
        error: "",
      };
    },
    async created() {
      await this.fetchStudents();
    },
    methods: {
      async fetchStudents() {
        try {
          const response = await api.get("/student/getAll");
          this.students = response.data;
        } catch (err) {
          this.error = "Failed to fetch students.";
        } finally {
          this.loading = false;
        }
      },
      async deleteUser(userId) {
        if (!confirm("Are you sure you want to delete this student?")) return;
  
        try {
          await api.post("/admin/user/delete", { userId });
          this.students = this.students.filter(student => student.id !== userId);
        } catch (err) {
          alert("Failed to delete user.");
        }
      },
    },
  };
  </script>
  