<script setup>
import { useRouter } from "vue-router"
import NotificationsDropdown from "../components/NotificationsDropdown.vue"

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

        <router-link to="/dashboard" class="block px-4 py-2 rounded hover:bg-gray-200">
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

        <router-link
          to="/profile"
          class="block px-4 py-2 text-gray-700 hover:text-indigo-600"
        >
          Profile
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

    <!-- MAIN AREA -->
    <div class="flex-1 flex flex-col">

      <!-- 🔥 TOP BAR -->
      <header class="bg-white shadow px-6 py-4 flex justify-between items-center">

        <h1 class="font-semibold text-lg">
          Task Manager
        </h1>

        <div class="flex items-center gap-4">

          <!-- 🔔 NOTIFICATIONS -->
          <NotificationsDropdown />

          <!-- USER -->
          <span class="text-sm text-gray-700">
            {{ user?.name }}
          </span>

        </div>

      </header>

      <!-- PAGE CONTENT -->
      <main class="p-10 flex-1">
        <slot />
      </main>

    </div>

  </div>
</template>