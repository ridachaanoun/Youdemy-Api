<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Category Management</h1>

          <!-- Add Category Section -->
    <div class="mb-6">
      <h2 class="text-lg font-semibold">Add a New Category</h2>
      <div class="flex gap-2 mt-2">
        <input v-model="newCategory" placeholder="Enter category name" class="border px-3 py-2 rounded w-full" />
        <button @click="addCategory" class="px-4 py-2 bg-blue-500 text-white rounded">Add</button>
      </div>
    </div>

    <!-- Loading or Error State -->
    <div v-if="loading" class="text-center">Loading...</div>
    <div v-else-if="error" class="text-red-500">{{ error }}</div>

    <!-- Category List -->
    <div v-else>
      <div v-if="categories.length" class="bg-white shadow-md rounded-lg p-4">
        <table class="w-full border-collapse">
          <thead>
            <tr class="bg-gray-100">
              <th class="border p-2 text-left">ID</th>
              <th class="border p-2 text-left">Category Name</th>
              <th class="border p-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="category in categories" :key="category.id" class="border-b">
              <td class="border p-2">{{ category.id }}</td>
              <td class="border p-2">
                <input v-if="editingCategoryId === category.id" v-model="category.name" class="border px-2 py-1 rounded w-full" />
                <span v-else>{{ category.name }}</span>
              </td>
              <td class="border p-2 text-center">
                <button v-if="editingCategoryId === category.id" @click="updateCategory(category)" class="px-3 py-1 bg-green-500 text-white rounded">Save</button>
                <button v-else @click="editCategory(category)" class="px-3 py-1 bg-yellow-500 text-white rounded">Edit</button>
                <button @click="deleteCategory(category.id)" class="ml-2 px-3 py-1 bg-red-500 text-white rounded">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <p v-else class="text-gray-500">No categories found.</p>
    </div>


  </div>
</template>

<script>
import api from "@/api";

export default {
  data() {
    return {
      categories: [],
      newCategory: "",
      editingCategoryId: null,
      loading: true,
      error: "",
    };
  },
  async created() {
    await this.fetchCategories();
  },
  methods: {
    async fetchCategories() {
      try {
        const response = await api.get("/category/list");
        this.categories = response.data;
      } catch (err) {
        this.error = "Failed to fetch categories.";
      } finally {
        this.loading = false;
      }
    },
    async addCategory() {
      if (!this.newCategory.trim()) {
        alert("Category name cannot be empty.");
        return;
      }

      try {
        await api.post("/admin/category/manage", { action: "add", categoryName: this.newCategory });
        this.categories.push({ id: this.categories.length + 1, name: this.newCategory }); // Add locally to UI
        this.newCategory = "";
      } catch (err) {
        alert("Failed to add category.");
      }
    },
    editCategory(category) {
      this.editingCategoryId = category.id;
    },
    async updateCategory(category) {
      try {
        await api.post("/admin/category/manage", { action: "update", categoryId: category.id, categoryName: category.name });
        this.editingCategoryId = null;
        alert("Category updated successfully!");
      } catch (err) {
        alert("Failed to update category.");
      }
    },
    async deleteCategory(categoryId) {
      if (!confirm("Are you sure you want to delete this category?")) return;

      try {
        await api.post("/admin/category/manage", { action: "delete", categoryId });
        this.categories = this.categories.filter(category => category.id !== categoryId);
        alert("Category deleted successfully!");
      } catch (err) {
        alert("Failed to delete category.");
      }
    },
  },
};
</script>
