<template>
  <div class="flex justify-center items-center h-screen bg-gray-100">
    <form
      class="bg-white p-8 rounded-lg shadow-md w-full max-w-sm"
      @submit.prevent="handleRegister"
    >
      <h2 class="text-2xl font-semibold mb-6">Register</h2>
      <div class="mb-4">
        <label class="block text-gray-700">Name</label>
        <input
          type="text"
          v-model="name"
          class="border rounded w-full p-2"
          required
        />
      </div>
      <div class="mb-4">
        <label class="block text-gray-700">Email</label>
        <input
          type="email"
          v-model="email"
          class="border rounded w-full p-2"
          required
        />
      </div>
      <div class="mb-4">
        <label class="block text-gray-700">Password</label>
        <input
          type="password"
          v-model="password"
          class="border rounded w-full p-2"
          required
        />
        <p v-if="passwordError" class="text-red-500 text-sm">{{ passwordError }}</p>
      </div>
      <div class="mb-4">
        <label class="block text-gray-700">Role</label>
        <select v-model="role" class="border rounded w-full p-2">
          <option value="student">Student</option>
          <option value="teacher">Teacher</option>
        </select>
      </div>
      <button
        type="submit"
        class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded w-full"
      >
        Register
      </button>
      <p class="text-gray-500 mt-4 text-sm">
        Already have an account? <router-link to="/login" class="text-blue-500 underline">Login</router-link>
      </p>
      <p v-if="errorMessage" class="text-red-500 mt-2">{{ errorMessage }}</p>
    </form>

    <!-- Popup Modal -->
    <div v-if="showPopup" class="fixed inset-0 flex justify-center items-center bg-black bg-opacity-50">
      <div class="bg-white p-6 rounded-lg shadow-lg">
        <h3 class="text-lg font-semibold">Success</h3>
        <p class="mt-2">{{ successMessage }}</p>
        <button @click="closePopup" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">OK</button>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../api';

export default {
data() {
  return {
    name: '',
    email: '',
    password: '',
    role: 'student',
    errorMessage: '',
    passwordError: '',
    showPopup: false,
    successMessage: '',
  };
},
methods: {
  async handleRegister() {
    this.passwordError = '';
    this.errorMessage = '';

    // Password validation
    if (this.password.length < 8) {
      this.passwordError = "Password must be at least 8 characters long.";
      return;
    }

    try {
      const response = await api.post('/user/register', {
        name: this.name,
        email: this.email,
        password: this.password,
        role: this.role,
        isValidated: true,
      });

      this.successMessage = response.data.message;
      this.showPopup = true; // Show popup
    } catch (error) {
      this.errorMessage = error.response?.data?.message || "Registration failed";
    }
  },
  closePopup() {
    this.showPopup = false;
    this.$router.push('/login'); 
  },
},
};
</script>
