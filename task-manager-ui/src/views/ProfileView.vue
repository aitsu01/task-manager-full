<script setup>
import { ref } from "vue"
import api from "../services/api"
import MainLayout from "../layouts/MainLayout.vue"

const currentUser = ref(JSON.parse(localStorage.getItem("user")))

const uploadAvatar = async (e) => {
  const file = e.target.files[0]
  if (!file) return

  const formData = new FormData()
  formData.append("avatar", file)

  try {
    const res = await api.post("/user/avatar", formData)

    currentUser.value.avatar = res.data.avatar
    localStorage.setItem("user", JSON.stringify(currentUser.value))

  } catch (err) {
    console.error(err)
    alert("Errore upload avatar")
  }
}
</script>

<template>
  <MainLayout>

    <div class="max-w-xl mx-auto mt-10 bg-white p-8 rounded-2xl shadow-lg">

      <h1 class="text-2xl font-bold mb-6 text-gray-800">
        My Profile
      </h1>

      <!-- AVATAR -->
      <div class="flex flex-col items-center mb-6">

        <img
          v-if="currentUser.avatar"
          :src="`http://127.0.0.1:8000/storage/${currentUser.avatar}`"
          class="w-24 h-24 rounded-full object-cover mb-4"
        />

        <div
          v-else
          class="w-24 h-24 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-3xl font-bold mb-4"
        >
          {{ currentUser.name.charAt(0).toUpperCase() }}
        </div>

        <input
          type="file"
          @change="uploadAvatar"
          class="text-sm"
        />

      </div>

      <!-- USER INFO -->
      <div class="space-y-3 text-gray-700">

        <p>
          <strong>Name:</strong> {{ currentUser.name }}
        </p>

        <p>
          <strong>Email:</strong> {{ currentUser.email }}
        </p>

        <p>
          <strong>Status:</strong> {{ currentUser.status }}
        </p>

        <p>
          <strong>Role ID:</strong> {{ currentUser.role_id }}
        </p>

      </div>

    </div>

  </MainLayout>
</template>