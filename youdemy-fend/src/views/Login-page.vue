<template>
  <div class="flex justify-center items-center h-screen bg-gray-100">
    <form
      class="bg-white p-8 rounded-lg shadow-md w-full max-w-sm"
      @submit.prevent="handleLogin"
    >
      <h2 class="text-2xl font-semibold mb-6">Login</h2>
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
      </div>
      <button
        type="submit"
        class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded w-full"
      >
        Login
      </button>
      <p class="text-gray-500 mt-4 text-sm">
        Don't have an account? <router-link to="/register" class="text-blue-500 underline">Register</router-link>
      </p>
      <p v-if="errorMessage" class="text-red-500 mt-2">{{ errorMessage }}</p>
    </form>
  </div>
</template>

<script>
import api from '../api';

export default {
data() {
  return {
    email: '',
    password: '',
    errorMessage: '',
  };
},
methods: {
  // Function to set a cookie
  setCookie(name, value, days) {
    const d = new Date();
    d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000)); // Set expiration time
    const expires = "expires=" + d.toUTCString();
    document.cookie = `${name}=${value}; ${expires}; path=/; secure; HttpOnly`;
  },

  async handleLogin() {
    this.errorMessage = ''; // Clear previous error message

    try {
      const response = await api.post('/user/login', {
        email: this.email,
        password: this.password,
      });

      const { api_key } = response.data;

      // Set token in cookie
      document.cookie = `api_key=${api_key}; path=/;`;

      // Redirect to courses page
      this.$router.push('/');
    } catch (error) {
      if (error.response) {
        // Server responded with a status code
        this.errorMessage = error.response.data.message || 'Login failed';
      } else if (error.request) {
        // Request was made but no response received
        this.errorMessage = 'No response from server';
      } else {
        // Other errors
        this.errorMessage = 'An error occurred during login';
      }
    }
  },
},
};
</script>
