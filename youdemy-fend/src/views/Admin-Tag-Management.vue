<template>
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-4">Tag Management</h1>
  
            <!-- Add Tag Section -->
      <div class="mb-6">
        <h2 class="text-lg font-semibold">Add a New Tag</h2>
        <div class="flex gap-2 mt-2">
          <input v-model="newTag" placeholder="Enter tag name" class="border px-3 py-2 rounded w-full" />
          <button @click="addTag" class="px-4 py-2 bg-blue-500 text-white rounded">Add</button>
        </div>
      </div>

      <!-- Loading or Error State -->
      <div v-if="loading" class="text-center">Loading...</div>
      <div v-else-if="error" class="text-red-500">{{ error }}</div>
    
      <!-- Tag List -->
      <div v-else>
        <div v-if="tags.length" class="bg-white shadow-md rounded-lg p-4">
          <table class="w-full border-collapse">
            <thead>
              <tr class="bg-gray-100">
                <th class="border p-2 text-left">ID</th>
                <th class="border p-2 text-left">Tag Name</th>
                <th class="border p-2">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="tag in tags" :key="tag.id" class="border-b">
                <td class="border p-2">{{ tag.id }}</td>
                <td class="border p-2">
                  <input v-if="editingTagId === tag.id" v-model="tag.name" class="border px-2 py-1 rounded w-full" />
                  <span v-else>{{ tag.name }}</span>
                </td>
                <td class="border p-2 text-center">
                  <button v-if="editingTagId === tag.id" @click="updateTag(tag)" class="px-3 py-1 bg-green-500 text-white rounded">Save</button>
                  <button v-else @click="editTag(tag)" class="px-3 py-1 bg-yellow-500 text-white rounded">Edit</button>
                  <button @click="deleteTag(tag.id)" class="ml-2 px-3 py-1 bg-red-500 text-white rounded">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <p v-else class="text-gray-500">No tags found.</p>
      </div>
  
    </div>
  </template>
  
  <script>
  import api from "@/api";
  
  export default {
    data() {
      return {
        tags: [],
        newTag: "",
        editingTagId: null,
        loading: true,
        error: "",
      };
    },
    async created() {
      await this.fetchTags();
    },
    methods: {
      async fetchTags() {
        try {
          const response = await api.get("/tag/get");
          this.tags = response.data;
        } catch (err) {
          this.error = "Failed to fetch tags.";
        } finally {
          this.loading = false;
        }
      },
      async addTag() {
        if (!this.newTag.trim()) {
          alert("Tag name cannot be empty.");
          return;
        }
  
        try {
          await api.post("/admin/tag/manage", { action: "add", tagName: this.newTag });
          this.tags.push({ id: this.tags.length + 1, name: this.newTag }); // Add locally to UI
          this.newTag = "";
        } catch (err) {
          alert("Failed to add tag.");
        }
      },
      editTag(tag) {
        this.editingTagId = tag.id;
      },
      async updateTag(tag) {
        try {
          await api.post("/admin/tag/manage", { action: "update", tagId: tag.id, tagName: tag.name });
          this.editingTagId = null;
          alert("Tag updated successfully!");
        } catch (err) {
          alert("Failed to update tag.");
        }
      },
      async deleteTag(tagId) {
        if (!confirm("Are you sure you want to delete this tag?")) return;
  
        try {
          await api.post("/admin/tag/manage", { action: "delete", tagId });
          this.tags = this.tags.filter(tag => tag.id !== tagId);
          alert("Tag deleted successfully!");
        } catch (err) {
          alert("Failed to delete tag.");
        }
      },
    },
  };
  </script>
  