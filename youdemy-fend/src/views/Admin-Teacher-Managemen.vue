<template>
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-4">Teacher Management</h1>
  
      <!-- Loading or Error State -->
      <div v-if="loading" class="text-center">Loading...</div>
      <div v-else-if="error" class="text-red-500">{{ error }}</div>
  
      <!-- Teacher List -->
      <div v-else>
        <div v-if="teachers.length" class="bg-white shadow-md rounded-lg p-4">
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
              <tr v-for="teacher in teachers" :key="teacher.id" class="border-b">
                <td class="border p-2">{{ teacher.id }}</td>
                <td class="border p-2">{{ teacher.name }}</td>
                <td class="border p-2">{{ teacher.email }}</td>
                <td class="border p-2">
                  <select 
                    v-model="teacher.status" 
                    @change="updateTeacherStatus(teacher)" 
                    class="border p-1 rounded">
                    <option value="active">Active</option>
                    <option value="suspended">Suspended</option>
                  </select>
                </td>
                <td class="border p-2">
                  <button 
                    @click="deleteUser(teacher.id)" 
                    class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                    Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <p v-else class="text-gray-500">No teachers found.</p>
      </div>
    </div>
  </template>
  
  <script>
  import api from "@/api"; // Adjust the path if necessary
  
  export default {
    data() {
      return {
        teachers: [],
        loading: true,
        error: "",
      };
    },
    async created() {
      await this.fetchTeachers();
    },
    methods: {
      async fetchTeachers() {
        try {
          const response = await api.get("/teacher/getAll");
          this.teachers = response.data;
        } catch (err) {
          this.error = "Failed to fetch teachers.";
        } finally {
          this.loading = false;
        }
      },
      async updateTeacherStatus(teacher) {
        try {
          await api.post("/admin/teacher/update", {
            teacherId: teacher.id,
            status: teacher.status,
          });
        } catch (err) {
          alert("Failed to update teacher status.");
        }
      },
      async deleteUser(userId) {
        if (!confirm("Are you sure you want to delete this teacher?")) return;
  
        try {
          await api.post("/admin/user/delete", { userId });
          this.teachers = this.teachers.filter(teacher => teacher.id !== userId);
        } catch (err) {
          alert("You cannot delete the teacher because they have courses. Delete the courses first.");
        }
      },
    },
  };
  </script>
