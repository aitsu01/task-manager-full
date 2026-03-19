<script setup>
import { ref, onMounted } from "vue"
import api from "../services/api"

const dashboard = ref(null)
const loading = ref(true)
const error = ref(null)

onMounted(async () => {
  try {
    const token = localStorage.getItem("token")

    const response = await api.get("/dashboard", {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })

    dashboard.value = response.data
  } catch (err) {
    error.value = "Errore nel caricamento dashboard"
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div class="min-h-screen bg-gray-100 p-10">

    <h1 class="text-3xl font-bold mb-8">Dashboard</h1>

    <!-- Loading -->
    <div v-if="loading" class="text-gray-500">Caricamento...</div>

    <!-- Error -->
    <div v-if="error" class="text-red-500">{{ error }}</div>

    <!-- Dashboard Content -->
    <div v-if="dashboard">

      <!-- KPI Cards -->
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

      <!-- Weekly Trend -->
      <div class="bg-white p-6 rounded-xl shadow mb-10">
        <h2 class="text-xl font-semibold mb-4">Weekly Completed</h2>

        <div class="flex gap-4 items-end h-40">
          <div
            v-for="day in dashboard.weekly_completed"
            :key="day.date"
            class="flex flex-col items-center flex-1"
          >
            <div
              class="bg-green-500 w-full rounded-t"
              :style="{ height: day.completed * 10 + 'px' }"
            ></div>
            <span class="text-xs mt-2">
              {{ day.date.slice(5) }}
            </span>
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
            class="border-b py-2 flex justify-between"
          >
            <span>{{ task.title }}</span>
            <span class="text-sm text-gray-500">
              {{ task.status }}
            </span>
          </li>
        </ul>
      </div>

    </div>
  </div>
</template>