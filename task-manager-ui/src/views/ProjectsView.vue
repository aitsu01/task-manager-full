<script setup>
import { ref, onMounted, computed } from "vue"
import api from "../services/api"
import MainLayout from "../layouts/MainLayout.vue"
import { RouterLink } from "vue-router"

const projects = ref([])
const loading = ref(true)

const showModal = ref(false)
const creating = ref(false)
const error = ref(null)

// 📅 DATA OGGI
const today = new Date().toISOString().split("T")[0]

// 🧩 NEW PROJECT
const newProject = ref({
  name: "",
  description: "",
  deadline: today
})

// 🔐 USER SAFE PARSE
let currentUser = null
try {
  currentUser = JSON.parse(localStorage.getItem("user"))
} catch {
  currentUser = null
}

const canCreateProject = currentUser?.status === "approved"

/* ---------------- FETCH PROJECTS ---------------- */
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

/* ---------------- SPLIT PROJECTS ---------------- */
const ownedProjects = computed(() =>
  projects.value.filter(p => p.role === "owner")
)

const sharedProjects = computed(() =>
  projects.value.filter(p => p.role !== "owner")
)

/* ---------------- OPEN MODAL ---------------- */
const openModal = () => {
  error.value = null
  showModal.value = true
}

/* ---------------- CREATE PROJECT ---------------- */
const createProject = async () => {
  if (!newProject.value.name) {
    error.value = "Il nome progetto è obbligatorio"
    return
  }

  if (newProject.value.deadline < today) {
    error.value = "La deadline non può essere nel passato"
    return
  }

  creating.value = true
  error.value = null

  try {
    await api.post("/projects", newProject.value)

    showModal.value = false

    newProject.value = {
      name: "",
      description: "",
      deadline: today
    }

    fetchProjects()

  } catch (err) {
    error.value = "Errore durante creazione progetto"
  } finally {
    creating.value = false
  }
}
</script>

<template>
  <MainLayout>

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold">Projects</h1>

      <button
        v-if="canCreateProject"
        @click="openModal"
        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
      >
        + Nuovo Progetto
      </button>
    </div>

    <div v-if="loading">Caricamento...</div>

    <!-- OWNED -->
    <div v-if="ownedProjects.length">
      <h2 class="text-xl font-semibold mb-4 text-gray-700">
        My Projects
      </h2>

      <RouterLink
        v-for="project in ownedProjects"
        :key="project.id"
        :to="`/projects/${project.id}`"
        class="block bg-white p-6 rounded-xl shadow mb-4 border-l-4 border-green-500 hover:shadow-lg transition"
      >
        <h2 class="text-lg font-semibold">{{ project.name }}</h2>
        <p class="text-gray-500 text-sm">{{ project.description }}</p>

        <div class="mt-3 text-sm text-gray-600 space-y-1">
          <p>Creato da: <strong>{{ project.creator?.name }}</strong></p>
          <p>Creato il: {{ new Date(project.created_at).toLocaleDateString() }}</p>

          <p v-if="project.deadline">
            Deadline: {{ new Date(project.deadline).toLocaleDateString() }}
          </p>
        </div>
      </RouterLink>
    </div>

    <!-- SHARED -->
    <div v-if="sharedProjects.length" class="mt-10">
      <h2 class="text-xl font-semibold mb-4 text-gray-700">
        Shared With Me
      </h2>

      <RouterLink
        v-for="project in sharedProjects"
        :key="project.id"
        :to="`/projects/${project.id}`"
        class="block bg-white p-6 rounded-xl shadow mb-4 border-l-4 border-blue-500 hover:shadow-lg transition"
      >
        <h2 class="text-lg font-semibold">{{ project.name }}</h2>
        <p class="text-gray-500 text-sm">{{ project.description }}</p>

        <div class="mt-3 text-sm text-gray-600 space-y-1">
          <p>Creato da: <strong>{{ project.creator?.name }}</strong></p>
          <p>Creato il: {{ new Date(project.created_at).toLocaleDateString() }}</p>

          <p v-if="project.deadline">
            Deadline: {{ new Date(project.deadline).toLocaleDateString() }}
          </p>
        </div>
      </RouterLink>
    </div>

    <!-- MODAL -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50"
    >
      <div class="bg-white w-96 p-6 rounded-2xl shadow-xl">

        <h2 class="text-xl font-semibold mb-4">
          Crea Nuovo Progetto
        </h2>

        <div class="space-y-4">

          <!-- NAME -->
          <div>
            <label class="text-sm text-gray-600">Nome progetto</label>
            <input v-model="newProject.name" class="w-full border rounded px-3 py-2" />
          </div>

          <!-- DESCRIPTION -->
          <div>
            <label class="text-sm text-gray-600">Descrizione</label>
            <textarea v-model="newProject.description" class="w-full border rounded px-3 py-2" />
          </div>

          <!-- DATA CREAZIONE -->
          <div>
            <label class="text-sm text-gray-600">Data creazione</label>
            <input
              type="date"
              :value="today"
              disabled
              class="w-full border rounded px-3 py-2 bg-gray-100 text-gray-500"
            />
          </div>

          <!-- DEADLINE -->
          <div>
            <label class="text-sm text-gray-600">Deadline</label>
            <input
              type="date"
              v-model="newProject.deadline"
              :min="today"
              class="w-full border rounded px-3 py-2"
            />
            <p class="text-xs text-gray-400 mt-1">Minimo: oggi</p>
          </div>

          <!-- ERROR -->
          <div v-if="error" class="bg-red-50 text-red-600 text-sm p-2 rounded">
            {{ error }}
          </div>

        </div>

        <!-- ACTIONS -->
        <div class="flex justify-end gap-3 mt-6">
          <button @click="showModal = false" class="px-4 py-2 rounded border">
            Annulla
          </button>

          <button
            @click="createProject"
            :disabled="creating"
            class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 disabled:opacity-50"
          >
            <span v-if="!creating">Crea</span>
            <span v-else>Creazione...</span>
          </button>
        </div>

      </div>
    </div>

  </MainLayout>
</template>