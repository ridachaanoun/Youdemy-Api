<template>
  <div class="min-h-screen bg-gray-100 text-gray-900">
    <nav class="bg-blue-600 text-white p-4">
      <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-2xl font-bold">Youdemy</h1>
        <ul class="flex space-x-4">
          <li><router-link to="/" class="hover:underline">Home</router-link></li>
          <li><router-link to="/courses" class="hover:underline">Courses</router-link></li>
          <li><router-link to="/about" class="hover:underline">About</router-link></li>
        </ul>
      </div>
    </nav>

    <div class="container mx-auto p-6">
      <router-view></router-view>
    </div>
  </div>
</template>

<script>
export default {
  name: "App",
};
</script>

<style>
</style>
