<script setup>
import { ref, onMounted, computed } from "vue"
import { useRoute } from "vue-router"
import api from "../services/api"
import MainLayout from "../layouts/MainLayout.vue"

const route = useRoute()
const projectId = route.params.id

const members = ref([])
const loading = ref(true)
const newMemberId = ref("")
const newRole = ref("member")

const currentUser = JSON.parse(localStorage.getItem("user"))

/*
|--------------------------------------------------------------------------
| Fetch Members
|--------------------------------------------------------------------------
*/
const fetchMembers = async () => {
  try {
    const res = await api.get(`/projects/${projectId}/members`)
    members.value = res.data
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}

/*
|--------------------------------------------------------------------------
| Permissions
|--------------------------------------------------------------------------
*/
const canManageMembers = computed(() => {
  // Admin globale (role_id = 1)
  if (currentUser?.role_id === 1) {
    return true
  }

  // Owner nel progetto
  const me = members.value.find(
    member => member.id === currentUser.id
  )

  return me?.role === "owner"
})

/*
|--------------------------------------------------------------------------
| Add Member
|--------------------------------------------------------------------------
*/
const addMember = async () => {
  if (!newMemberId.value) return

  try {
    await api.post(`/projects/${projectId}/members`, {
      user_id: newMemberId.value,
      role: newRole.value
    })

    newMemberId.value = ""
    newRole.value = "member"
    fetchMembers()

  } catch (err) {
    if (err.response?.status === 400) {
      alert("Utente già membro del progetto")
    } else if (err.response?.status === 403) {
      alert("Non autorizzato")
    } else {
      alert("Errore durante aggiunta membro")
    }
  }
}

/*
|--------------------------------------------------------------------------
| Update Role
|--------------------------------------------------------------------------
*/
const updateRole = async (member) => {
  try {
    await api.patch(`/projects/${projectId}/members/${member.id}`, {
      role: member.role
    })

    fetchMembers()
  } catch (err) {
    console.error(err)
  }
}

/*
|--------------------------------------------------------------------------
| Remove Member
|--------------------------------------------------------------------------
*/
const removeMember = async (member) => {
  if (!confirm("Rimuovere questo membro?")) return

  try {
    await api.delete(`/projects/${projectId}/members/${member.id}`)
    fetchMembers()
  } catch (err) {
    console.error(err)
  }
}

onMounted(fetchMembers)
</script>

<template>
  <MainLayout>

    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold">Project Members</h1>
    </div>

    <!-- Avviso -->
    <p
      v-if="!canManageMembers"
      class="text-gray-500 text-sm mb-4"
    >
      Solo l'owner del progetto può gestire i membri.
    </p>

    <div v-if="loading">Caricamento...</div>

    <!-- LISTA MEMBRI -->
    <div
      v-for="member in members"
      :key="member.id"
      class="bg-white p-6 rounded-xl shadow mb-4"
    >
      <div class="flex justify-between items-center">

        <!-- INFO -->
        <div>
          <p class="font-semibold">{{ member.name }}</p>
          <p class="text-gray-500 text-sm">{{ member.email }}</p>

          <span
            class="inline-block mt-2 px-2 py-1 text-xs rounded font-medium"
            :class="{
              'bg-purple-100 text-purple-700': member.role === 'owner',
              'bg-blue-100 text-blue-700': member.role === 'manager',
              'bg-gray-100 text-gray-700': member.role === 'member'
            }"
          >
            {{ member.role }}
          </span>
        </div>

        <!-- ACTIONS -->
        <div
          v-if="member.role !== 'owner' && canManageMembers"
          class="flex gap-2 items-center"
        >
          <select
            v-model="member.role"
            class="border rounded px-2 py-1"
          >
            <option value="manager">Manager</option>
            <option value="member">Member</option>
          </select>

          <button
            @click="updateRole(member)"
            class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700"
          >
            Aggiorna
          </button>

          <button
            @click="removeMember(member)"
            class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700"
          >
            Rimuovi
          </button>
        </div>

      </div>
    </div>

    <!-- AGGIUNGI MEMBRO -->
    <div
      v-if="canManageMembers"
      class="bg-white p-6 rounded-xl shadow mt-6"
    >
      <h2 class="text-lg font-semibold mb-4">Aggiungi membro</h2>

      <div class="flex gap-3">
        <input
          v-model="newMemberId"
          placeholder="User ID"
          class="border rounded px-3 py-2 w-32"
        />

        <select
          v-model="newRole"
          class="border rounded px-3 py-2"
        >
          <option value="manager">Manager</option>
          <option value="member">Member</option>
        </select>

        <button
          @click="addMember"
          class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
        >
          Aggiungi
        </button>
      </div>
    </div>

  </MainLayout>
</template>