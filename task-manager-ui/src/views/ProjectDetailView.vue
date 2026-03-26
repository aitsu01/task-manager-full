<script setup>
import { ref, onMounted } from "vue"
import { useRoute } from "vue-router"
import api from "../services/api"
import MainLayout from "../layouts/MainLayout.vue"

const route = useRoute()
const projectId = route.params.id

const tasks = ref([])
const project = ref(null)
const loading = ref(true)

const currentUser = JSON.parse(localStorage.getItem("user"))





const newTask = ref({
  title: "",
  description: "",
  status: "todo",
  due_date: ""
})

/* ---------------- FETCH PROJECT ---------------- */
const fetchProject = async () => {
  const res = await api.get(`/projects/${projectId}`)
  project.value = res.data.data
}

/* ---------------- FETCH TASKS ---------------- */
const fetchTasks = async () => {
  const res = await api.get(`/projects/${projectId}/tasks`)
  tasks.value = res.data.data
}

/* ---------------- CREATE TASK ---------------- */
const createTask = async () => {
  if (!newTask.value.title) return

  await api.post(`/projects/${projectId}/tasks`, newTask.value)

  newTask.value = {
    title: "",
    description: "",
    status: "todo",
    due_date: ""
  }

  fetchTasks()
}

/* ---------------- UPDATE STATUS ---------------- */
const updateStatus = async (task, status) => {
  await api.patch(`/projects/${projectId}/tasks/${task.id}`, {
    status
  })

  fetchTasks()
}

/* ---------------- DELETE TASK ---------------- */
const deleteTask = async (task) => {
  if (!confirm("Eliminare questa task?")) return

  await api.delete(`/projects/${projectId}/tasks/${task.id}`)
  fetchTasks()
}

/* ---------------- PERMISSIONS ---------------- */
const isOwner = () => {
  return project.value?.role === "owner"
}

const canEditTask = (task) => {
  return isOwner() || task.assigned_user?.id === currentUser.id
}

onMounted(async () => {
  await fetchProject()
  await fetchTasks()
  loading.value = false
})







</script>

<template>
  <MainLayout>

    <h1 class="text-3xl font-bold mb-6">
      {{ project?.name }}
    </h1>

    <!-- ADD TASK -->
    <div class="bg-white p-6 rounded-xl shadow mb-8">
      <h2 class="text-lg font-semibold mb-4">Aggiungi Task</h2>






      <input
        v-model="newTask.title"
        placeholder="Titolo"
        class="border px-3 py-2 rounded w-full mb-3"
      />

      <textarea
        v-model="newTask.description"
        placeholder="Descrizione"
        class="border px-3 py-2 rounded w-full mb-3"
      />

      <input
        type="date"
        v-model="newTask.due_date"
        class="border px-3 py-2 rounded w-full mb-3"
      />

      <button
        @click="createTask"
        class="bg-green-600 text-white px-4 py-2 rounded"
      >
        Crea Task
      </button>
    </div>

    <!-- TASK LIST -->
    <div v-if="loading">Caricamento...</div>

    <div
      v-for="task in tasks"
      :key="task.id"
      class="bg-white p-6 rounded-xl shadow mb-4"
    >
      <div class="flex justify-between items-center">

        <div>
          <h3 class="font-semibold">{{ task.title }}</h3>

          <p class="text-gray-500 text-sm">
            {{ task.description }}
          </p>

          <div class="mt-2 text-xs text-gray-600">
            Stato: <span class="font-medium">{{ task.status }}</span>
          </div>

          <div v-if="task.assigned_user" class="text-xs text-gray-600">
            Assegnato a: {{ task.assigned_user.name }}
          </div>
        </div>

        <div class="flex gap-2">

          <button
            v-if="canEditTask(task)"
            @click="updateStatus(task, 'doing')"
            class="bg-blue-500 text-white px-3 py-1 rounded text-xs"
          >
            Doing
          </button>

          <button
            v-if="canEditTask(task)"
            @click="updateStatus(task, 'done')"
            class="bg-green-500 text-white px-3 py-1 rounded text-xs"
          >
            Done
          </button>

          <button
            v-if="isOwner()"
            @click="deleteTask(task)"
            class="bg-red-600 text-white px-3 py-1 rounded text-xs"
          >
            Elimina
          </button>

        </div>
      </div>
    </div>

  </MainLayout>
</template>