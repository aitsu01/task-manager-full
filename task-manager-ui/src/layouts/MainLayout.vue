<script setup>
import { useRouter } from "vue-router"

const router = useRouter()

const user = JSON.parse(localStorage.getItem("user") || "null")

const isAdmin = user?.role?.name === "admin" || user?.role_id === 1

const logout = () => {
  localStorage.removeItem("token")
  localStorage.removeItem("user")
  router.push("/login")
}
</script>

<template>
  <div class="flex min-h-screen bg-gray-100">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-white shadow-lg flex flex-col">

      <!-- User Info -->
      <div class="p-6 border-b">
        <p class="font-semibold text-lg">
          {{ user?.name || "Utente" }}
        </p>
        <p class="text-sm text-gray-500">
          {{ user?.email }}
        </p>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 p-4 space-y-2">

        <router-link
          to="/dashboard"
          class="block px-4 py-2 rounded hover:bg-gray-200"
        >
          Dashboard
        </router-link>

        <router-link
          v-if="isAdmin"
          to="/admin/users"
          class="block px-4 py-2 rounded hover:bg-gray-200"
        >
          Admin Panel
        </router-link>

        <router-link to="/projects" class="block px-4 py-2 rounded hover:bg-gray-200">
         Projects
        </router-link>

      </nav>

      <!-- Logout -->
      <div class="p-4 border-t">
        <button
          @click="logout"
          class="w-full bg-red-500 text-white py-2 rounded hover:bg-red-600"
        >
          Logout
        </button>
      </div>

    </aside>

    <!-- CONTENT -->
    <main class="flex-1 p-10">
      <slot />
    </main>

  </div>
</template>