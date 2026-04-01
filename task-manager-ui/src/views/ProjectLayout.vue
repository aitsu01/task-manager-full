<script setup>
import { useRoute } from "vue-router"

const route = useRoute()
const projectId = route.params.id
</script>

<template>
  <div class="p-6">

    <!-- 🔥 TABS NAVIGATION -->
    <div class="flex items-center gap-4 mb-6 border-b pb-3">

      <!-- Dashboard -->
      <RouterLink
        to="/dashboard"
        class="px-3 py-1 rounded text-sm font-medium"
        :class="$route.path === '/dashboard'
          ? 'bg-gray-800 text-white'
          : 'text-gray-600 hover:bg-gray-100'"
      >
        Dashboard
      </RouterLink>

      <!-- Task -->
      <RouterLink
        :to="`/projects/${projectId}`"
        class="px-3 py-1 rounded text-sm font-medium"
        :class="$route.path === `/projects/${projectId}`
          ? 'bg-blue-600 text-white'
          : 'text-gray-600 hover:bg-gray-100'"
      >
        Task
      </RouterLink>

      <!-- Members -->
      <RouterLink
        :to="`/projects/${projectId}/members`"
        class="px-3 py-1 rounded text-sm font-medium"
        :class="$route.path.includes('members')
          ? 'bg-blue-600 text-white'
          : 'text-gray-600 hover:bg-gray-100'"
      >
        Members
      </RouterLink>

    </div>

    <!-- CONTENUTO PAGINA -->
    <RouterView />

  </div>
</template>