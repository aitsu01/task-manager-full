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
const error = ref(null)

const currentUser = JSON.parse(localStorage.getItem("user"))

const newTask = ref({
  title: "",
  description: "",
  status: "todo",
  due_date: ""
})

/* ---------------- FETCH PROJECT ---------------- */
const fetchProject = async () => {
  try {
    const res = await api.get(`/projects/${projectId}`)
    project.value = res.data.data
  } catch (err) {
    if (err.response?.status === 403) {
      error.value = "forbidden"
    } else if (err.response?.status === 404) {
      error.value = "not_found"
    } else {
      error.value = "generic"
    }
  }
}

/* ---------------- FETCH TASKS ---------------- */
const fetchTasks = async () => {
  try {
    const res = await api.get(`/projects/${projectId}/tasks`)
    tasks.value = res.data.data
  } catch (err) {
    console.error(err)
  }
}

/* ---------------- CREATE TASK ---------------- */
const createTask = async () => {
  if (!newTask.value.title) return

  try {
    const res = await api.post(`/projects/${projectId}/tasks`, newTask.value)

    tasks.value.push(res.data.data)

    newTask.value = {
      title: "",
      description: "",
      status: "todo",
      due_date: ""
    }
  } catch (err) {
    console.error(err)
  }
}

/* ---------------- UPDATE STATUS ---------------- */
const updateStatus = async (task, status) => {
  try {
    await api.patch(`/projects/${projectId}/tasks/${task.id}`, { status })
    task.status = status
  } catch (err) {
    console.error(err)
  }
}

/* ---------------- DELETE TASK ---------------- */
const deleteTask = async (task) => {
  if (!confirm("Eliminare questa task?")) return

  try {
    await api.delete(`/projects/${projectId}/tasks/${task.id}`)
    tasks.value = tasks.value.filter(t => t.id !== task.id)
  } catch (err) {
    console.error(err)
  }
}

/* ---------------- PERMISSIONS ---------------- */
const isOwner = () => {
  return project.value?.role === "owner"
}

const canEditTask = (task) => {
  return isOwner() || task.assigned_user?.id === currentUser.id
}

/* ---------------- INIT ---------------- */
onMounted(async () => {
  await fetchProject()

  if (!error.value) {
    await fetchTasks()
  }

  loading.value = false
})
</script>

<template>
  <MainLayout>

    <!-- ERROR STATES -->
    <div v-if="error === 'forbidden'" class="text-center mt-10">
      <h2 class="text-xl font-semibold text-red-600">
        Non hai accesso a questo progetto
      </h2>
    </div>

    <div v-else-if="error === 'not_found'" class="text-center mt-10">
      Progetto non trovato
    </div>

    <div v-else-if="error === 'generic'" class="text-center mt-10">
      Errore imprevisto
    </div>

    <!-- CONTENUTO -->
    <div v-else>

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

      <div v-else-if="tasks.length === 0" class="text-gray-500">
        Nessuna task presente
      </div>

      <div
        v-else
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

    </div>

  </MainLayout>
</template>