<script setup>
import { ref, onMounted } from "vue"
import api from "../services/api"

const notifications = ref([])
const open = ref(false)

const fetchNotifications = async () => {
  const res = await api.get("/notifications")
  notifications.value = res.data
}

const markAsRead = async (id) => {
  await api.post(`/notifications/read/${id}`)
  notifications.value = notifications.value.filter(n => n.id !== id)
}

onMounted(fetchNotifications)
</script>

<template>
  <div class="relative">
    
    <!-- ICON -->
    <button @click="open = !open" class="relative text-xl">
      🔔
      <span
        v-if="notifications.length"
        class="absolute -top-1 -right-1 bg-red-500 text-white text-xs px-1 rounded-full"
      >
        {{ notifications.length }}
      </span>
    </button>

    <!-- DROPDOWN -->
    <div
      v-if="open"
      class="absolute right-0 mt-2 w-80 bg-white shadow-xl rounded-lg p-4 z-50"
    >
      <div v-if="notifications.length === 0" class="text-gray-500 text-sm">
        Nessuna notifica
      </div>

      <div
        v-for="n in notifications"
        :key="n.id"
        class="border-b py-2 text-sm cursor-pointer hover:bg-gray-50"
        @click="markAsRead(n.id)"
      >
        <p>
          Task <strong>{{ n.data.task_title }}</strong>
        </p>
        <p class="text-gray-500 text-xs">
          {{ n.data.old_status }} → {{ n.data.new_status }}
        </p>
      </div>
    </div>

  </div>
</template>