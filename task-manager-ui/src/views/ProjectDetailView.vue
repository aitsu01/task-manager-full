<script setup>
import { ref, onMounted } from "vue"
import { useRoute } from "vue-router"
import api from "../services/api"

const route = useRoute()
const projectId = route.params.id

const tasks = ref([])
const project = ref(null)
const loading = ref(true)
const error = ref(null)
const updatingTaskId = ref(null)

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
    if (err.response?.status === 403) error.value = "forbidden"
    else if (err.response?.status === 404) error.value = "not_found"
    else error.value = "generic"
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
  if (!newTask.value.title) {
    alert("Inserisci un titolo")
    return
  }

  try {
    const payload = {
      title: newTask.value.title,
      description: newTask.value.description,
      status: "todo",
      due_date: newTask.value.due_date || null // 🔥 FIX CRITICO
    }

    console.log("PAYLOAD:", payload)

    const res = await api.post(`/projects/${projectId}/tasks`, payload)

    console.log("RISPOSTA:", res.data)

    tasks.value.unshift(res.data.data)

    newTask.value = {
      title: "",
      description: "",
      status: "todo",
      due_date: ""
    }

  } catch (err) {
    console.error("ERRORE BACKEND:", err.response?.data)
    alert("Errore creazione task")
  }
}

/* ---------------- CHANGE STATUS (OWNER ONLY) ---------------- */
const changeStatus = async (task, status) => {
  updatingTaskId.value = task.id

  try {
    await api.patch(`/projects/${projectId}/tasks/${task.id}/status`, {
      status
    })

    // update locale
    task.status = status

  } catch (err) {
    alert("Errore aggiornamento")
  } finally {
    updatingTaskId.value = null
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
  return project.value?.creator_id === currentUser.id
}

/* ---------------- STATUS COLORS ---------------- */
const statusColor = (status) => {
  if (status === "todo") return "bg-gray-500"
  if (status === "doing") return "bg-yellow-500"
  if (status === "done") return "bg-green-600"
  return "bg-gray-400"
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

    <!-- 🔥 ADD TASK (TUTTI I MEMBRI) -->
    <div class="bg-white p-6 rounded-xl shadow mb-8">
      <h2 class="text-lg font-semibold mb-4">+ Nuova Task</h2>

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
        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
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

        <!-- INFO -->
        <div>
          <h3 class="font-semibold">{{ task.title }}</h3>

          <p class="text-gray-500 text-sm">
            {{ task.description }}
          </p>

          <div class="mt-2 text-xs text-gray-600">
            Due: {{ task.due_date }}
          </div>

          <!-- 🔥 LABEL -->
          <div
            v-if="task.status === 'todo'"
            class="text-xs text-yellow-600 mt-1"
          >
            ⏳ In attesa approvazione
          </div>
        </div>

        <!-- ACTIONS -->
        <div class="flex gap-3 items-center">

          <!-- 👑 OWNER -->
          <div v-if="isOwner()" class="flex gap-2">

            <!-- APPROVA -->
            <button
              v-if="task.status === 'todo'"
              @click="changeStatus(task, 'doing')"
              :disabled="updatingTaskId === task.id"
              class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600"
            >
              Approva
            </button>

            <!-- COMPLETA -->
            <button
              v-if="task.status !== 'done'"
              @click="changeStatus(task, 'done')"
              :disabled="updatingTaskId === task.id"
              class="bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700"
            >
              Completa
            </button>

            <!-- DELETE -->
            <button
              @click="deleteTask(task)"
              class="bg-red-600 text-white px-3 py-1 rounded text-xs hover:bg-red-700"
            >
              Elimina
            </button>

          </div>

          <!-- 👤 MEMBER -->
          <span
            v-else
            class="text-sm px-2 py-1 rounded text-white"
            :class="statusColor(task.status)"
          >
            {{ task.status }}
          </span>

        </div>

      </div>
    </div>

  </div>

</template>