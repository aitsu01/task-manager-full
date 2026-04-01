<script setup>
import { ref, onMounted } from "vue"
import api from "../services/api"
import MainLayout from "../layouts/MainLayout.vue"

const currentUser = JSON.parse(localStorage.getItem("user"))

const dashboard = ref(null)
const loading = ref(true)
const error = ref(null)
const updatingTaskId = ref(null)

onMounted(async () => {
  try {
    const response = await api.get("/dashboard")
    dashboard.value = response.data
  } catch (err) {
    error.value = "Errore nel caricamento dashboard"
  } finally {
    loading.value = false
  }
})

const updateStatus = async (task) => {
  updatingTaskId.value = task.id

  try {
    await api.patch(`/tasks/${task.id}/status`, {
      status: task.status
    })
  } catch (err) {
    alert("Non autorizzato o errore aggiornamento")
  } finally {
    updatingTaskId.value = null
  }
}

const statusColor = (status) => {
  if (status === "todo") return "bg-gray-500"
  if (status === "doing") return "bg-yellow-500"
  if (status === "done") return "bg-green-600"
  return "bg-gray-400"
}
</script>

<template>
  <MainLayout>

    <h1 class="text-3xl font-bold mb-8">Dashboard</h1>

    <div v-if="loading" class="text-gray-500">Caricamento...</div>
    <div v-if="error" class="text-red-500">{{ error }}</div>

    <div v-if="dashboard">

      <!-- KPI -->
      <div class="grid grid-cols-4 gap-6 mb-10">
        <div class="bg-white p-6 rounded-xl shadow">
          <p class="text-gray-500">Projects</p>
          <p class="text-2xl font-bold">{{ dashboard.total_projects }}</p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow">
          <p class="text-gray-500">Tasks</p>
          <p class="text-2xl font-bold">{{ dashboard.total_tasks }}</p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow">
          <p class="text-gray-500">Completion</p>
          <p class="text-2xl font-bold">
            {{ dashboard.completion_rate }}%
          </p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow">
          <p class="text-gray-500">Overdue</p>
          <p class="text-2xl font-bold text-red-500">
            {{ dashboard.overdue_tasks }}
          </p>
        </div>
      </div>

      <!-- Tasks per Status -->
      <div class="bg-white p-6 rounded-xl shadow mb-10">
        <h2 class="text-xl font-semibold mb-4">Tasks Status</h2>

        <div v-for="(value, status) in dashboard.tasks_per_status"
             :key="status"
             class="mb-4">

          <div class="flex justify-between mb-1">
            <span class="capitalize">{{ status }}</span>
            <span>{{ value.count }} ({{ value.percentage }}%)</span>
          </div>

          <div class="w-full bg-gray-200 rounded-full h-3">
            <div
              class="bg-blue-600 h-3 rounded-full"
              :style="{ width: value.percentage + '%' }"
            ></div>
          </div>

        </div>
      </div>

      <!-- Recent Tasks -->
      <div class="bg-white p-6 rounded-xl shadow">
        <h2 class="text-xl font-semibold mb-4">Recent Tasks</h2>

        <ul>
          <li
            v-for="task in dashboard.recent_tasks"
            :key="task.id"
            class="border-b py-3 flex justify-between items-center"
          >

            <span class="font-medium">{{ task.title }}</span>

            <!-- OWNER -->
            <select
              v-if="task.project?.owner_id === currentUser.id"
              v-model="task.status"
              @change="updateStatus(task)"
              :disabled="updatingTaskId === task.id"
              class="text-sm border rounded px-2 py-1"
            >
              <option value="todo">To Do</option>
              <option value="doing">Doing</option>
              <option value="done">Done</option>
            </select>

            <!-- MEMBER -->
            <span
              v-else
              class="text-sm px-2 py-1 rounded text-white"
              :class="statusColor(task.status)"
            >
              {{ task.status }}
            </span>

          </li>
        </ul>
      </div>

    </div>

  </MainLayout>
</template>
