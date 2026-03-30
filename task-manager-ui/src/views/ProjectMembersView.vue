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

/* ---------------- FETCH MEMBERS ---------------- */
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

/* ---------------- PERMISSIONS ---------------- */
const canManageMembers = computed(() => {
  // Admin globale (role_id = 1)
  if (currentUser?.role_id === 1) return true

  const me = members.value.find(
    member => member.id === currentUser.id
  )

  return me?.role === "owner"
})

/* ---------------- ADD MEMBER ---------------- */
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

/* ---------------- UPDATE ROLE ---------------- */
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

/* ---------------- REMOVE MEMBER ---------------- */
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

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-10">
      <h1 class="text-3xl font-bold text-gray-800">
        Project Members
      </h1>
    </div>

    <div v-if="loading" class="text-gray-500">
      Caricamento...
    </div>

    <!-- MEMBERS LIST -->
    <div
      v-for="member in members"
      :key="member.id"
      class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-4 hover:shadow-md transition"
    >
      <div class="flex justify-between items-center">

        <!-- MEMBER INFO -->
        <div class="flex items-center gap-4">
          <!-- Avatar -->
          <div
            class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-lg"
          >
            {{ member.name.charAt(0).toUpperCase() }}
          </div>

          <div>
            <p class="font-semibold text-gray-800">
              {{ member.name }}
            </p>
            <p class="text-gray-500 text-sm">
              {{ member.email }}
            </p>

            <!-- Role Badge -->
            <span
              class="inline-block mt-2 px-3 py-1 text-xs rounded-full font-medium"
              :class="{
                'bg-purple-100 text-purple-700': member.role === 'owner',
                'bg-blue-100 text-blue-700': member.role === 'manager',
                'bg-gray-100 text-gray-700': member.role === 'member'
              }"
            >
              {{ member.role }}
            </span>
          </div>
        </div>

        <!-- ACTIONS -->
        <div
          v-if="member.role !== 'owner' && canManageMembers"
          class="flex gap-3 items-center"
        >
          <select
            v-model="member.role"
            class="border border-gray-200 rounded-lg px-3 py-1 focus:ring-2 focus:ring-indigo-500 outline-none"
          >
            <option value="manager">Manager</option>
            <option value="member">Member</option>
          </select>

          <button
            @click="updateRole(member)"
            class="bg-indigo-600 text-white px-4 py-1 rounded-lg text-sm hover:bg-indigo-700 transition"
          >
            Save
          </button>

          <button
            @click="removeMember(member)"
            class="bg-red-500 text-white px-4 py-1 rounded-lg text-sm hover:bg-red-600 transition"
          >
            Remove
          </button>
        </div>

      </div>
    </div>

    <!-- ADD MEMBER SECTION -->
    <div
      v-if="canManageMembers"
      class="bg-white p-8 rounded-2xl shadow-lg border border-gray-100 mt-10"
    >
      <h2 class="text-xl font-semibold mb-6 text-gray-800">
        Add New Member
      </h2>

      <div class="flex gap-4 items-center">
        <input
          v-model="newMemberId"
          placeholder="User ID"
          class="border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 outline-none w-40"
        />

        <select
          v-model="newRole"
          class="border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 outline-none"
        >
          <option value="manager">Manager</option>
          <option value="member">Member</option>
        </select>

        <button
          @click="addMember"
          class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition shadow-sm"
        >
          Add Member
        </button>
      </div>
    </div>

  </MainLayout>
</template>