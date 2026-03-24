<script setup>
import { ref, onMounted } from "vue"
import api from "../services/api"
import MainLayout from "../layouts/MainLayout.vue"

const users = ref([])
const loading = ref(true)

const fetchData = async () => {
  try {
    const res = await api.get("/admin/users")
    users.value = res.data
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}

const approve = async (user) => {
  await api.patch(`/admin/users/${user.id}/approve`, {
    role_id: user.role_id
  })
  fetchData()
}

const reject = async (user) => {
  await api.patch(`/admin/users/${user.id}/reject`)
  fetchData()
}

const updateRole = async (user) => {
  if (!user.role_id) return alert("Seleziona un ruolo")

  await api.patch(`/admin/users/${user.id}/role`, {
    role_id: user.role_id
  })

  fetchData()
}

const deleteUser = async (user) => {
  if (!confirm("Sei sicuro di voler eliminare questo utente?")) return

  await api.delete(`/admin/users/${user.id}`)
  fetchData()
}

onMounted(fetchData)
</script>



<template>
  <MainLayout>

    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold">User Management</h1>
    </div>

    <div v-if="loading">Caricamento...</div>

    <div
      v-for="user in users"
      :key="user.id"
      class="bg-white p-6 rounded-xl shadow mb-4"
    >
      <div class="flex justify-between items-center">

        <!-- USER INFO -->
        <div>
          <p class="font-semibold">{{ user.name }}</p>
          <p class="text-gray-500 text-sm">{{ user.email }}</p>

          <!-- STATUS BADGE -->
          <span
            class="inline-block mt-2 px-2 py-1 text-xs rounded font-medium"
            :class="{
              'bg-yellow-100 text-yellow-700': user.status === 'pending',
              'bg-green-100 text-green-700': user.status === 'approved',
              'bg-red-100 text-red-700': user.status === 'rejected'
            }"
          >
            {{ user.status }}
          </span>

          <!-- CURRENT ROLE -->
          <p class="text-sm mt-2 text-gray-600">
            Ruolo attuale:
            <span class="font-medium">
              {{ user.role?.name || "Nessuno" }}
            </span>
          </p>
        </div>

        <!-- ACTIONS -->
        <div class="flex items-center gap-3">

          <select
            v-model="user.role_id"
            class="border rounded px-2 py-1"
          >
            <option disabled value="">Seleziona ruolo</option>
            <option value="1">Admin</option>
            <option value="2">User</option>
            <option value="3">Manager</option>
          </select>

          <button
            v-if="user.status === 'pending'"
            @click="approve(user)"
            class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700"
          >
            Approva
          </button>

          <button
            v-if="user.status === 'approved'"
            @click="updateRole(user)"
            class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700"
          >
            Aggiorna ruolo
          </button>

          <button
            v-if="user.status !== 'rejected'"
            @click="reject(user)"
            class="bg-yellow-600 text-white px-3 py-1 rounded hover:bg-yellow-700"
          >
            Rifiuta
          </button>

          <button
            @click="deleteUser(user)"
            class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700"
          >
            Elimina
          </button>

        </div>

      </div>
    </div>

  </MainLayout>
</template>