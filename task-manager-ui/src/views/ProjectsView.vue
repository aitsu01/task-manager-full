<script setup>
import { ref, onMounted } from "vue"
import api from "../services/api"
import MainLayout from "../layouts/MainLayout.vue"

const projects = ref([])
const loading = ref(true)

const fetchProjects = async () => {
  try {
    const res = await api.get("/projects")
    projects.value = res.data.data
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}

onMounted(fetchProjects)
</script>

<template>
  <MainLayout>

    <h1 class="text-3xl font-bold mb-8">Projects</h1>

    <div v-if="loading">Caricamento...</div>

    <div
      v-for="project in projects"
      :key="project.id"
      class="bg-white p-6 rounded-xl shadow mb-4"
    >
      <div class="flex justify-between items-center">

        <div>
          <h2 class="text-lg font-semibold">
            {{ project.name }}
          </h2>
          <p class="text-gray-500 text-sm">
            {{ project.description }}
          </p>
        </div>

        <div class="flex gap-2">

          <!-- 👥 MEMBERS -->
          <router-link
            :to="`/projects/${project.id}/members`"
            class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700"
          >
            Membri
          </router-link>

        </div>

      </div>
    </div>

  </MainLayout>
</template>