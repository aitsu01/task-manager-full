<script setup>
import { ref, onMounted } from "vue"
import api from "../services/api"

const notifications = ref([])
const unreadCount = ref(0)
const open = ref(false)

import { useRouter } from "vue-router"

const router = useRouter()

/* ---------------- FETCH ---------------- */
const fetchNotifications = async () => {
  try {
    const res = await api.get("/notifications")
    notifications.value = res.data

    //  conta non lette
    unreadCount.value = res.data.filter(n => !n.read_at).length

  } catch (err) {
    console.error(err)
  }
}

/* ---------------- MARK AS READ ---------------- */


const markAsRead = async (notification) => {
  try {
    await api.post(`/notifications/read/${notification.id}`)

    //  QUI VA QUESTA RIGA
    router.push(`/projects/${notification.data.project_id}?task=${notification.data.task_id}`)

    // aggiorna lista
    notifications.value = notifications.value.filter(n => n.id !== notification.id)
    unreadCount.value--

    // chiudi dropdown
    open.value = false

  } catch (err) {
    console.error(err)
  }
}



/* ---------------- TOGGLE ---------------- */
const toggleDropdown = () => {
  open.value = !open.value
}

onMounted(fetchNotifications)
</script>

<template>
  <div class="relative">

    <!-- 🔔 BUTTON -->
    <button @click="toggleDropdown" class="relative text-xl">
      🔔

      <!-- 🔴 BADGE -->
      <span
        v-if="unreadCount > 0"
        class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center"
      >
        {{ unreadCount }}
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
        @click="markAsRead(n)"
      >
        <p>
          {{ n.data.message }}
        </p>
      </div>
    </div>

  </div>
</template>