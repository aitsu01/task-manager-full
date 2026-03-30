<script setup>
import { ref, nextTick, onBeforeUnmount } from "vue"
import Cropper from "cropperjs"
import "cropperjs/dist/cropper.css"

import api from "../services/api"
import MainLayout from "../layouts/MainLayout.vue"

/* ---------------- SAFE USER LOAD ---------------- */
const storedUser = localStorage.getItem("user")
const currentUser = ref(storedUser ? JSON.parse(storedUser) : null)

if (!currentUser.value) {
  console.error("User not found in localStorage")
}

/* ---------------- AVATAR ---------------- */
const preview = ref(null)
const cropper = ref(null)
const image = ref(null)
const uploading = ref(false)

const handleFileChange = async (e) => {
  const file = e.target.files[0]
  if (!file) return

  preview.value = URL.createObjectURL(file)

  await nextTick()

  if (!image.value) return

  if (cropper.value) {
    cropper.value.destroy()
  }

  cropper.value = new Cropper(image.value, {
    aspectRatio: 1,
    viewMode: 1,
    background: false,
    autoCropArea: 1,
  })
}

const uploadAvatar = async () => {
  if (!cropper.value) return

  uploading.value = true

  const canvas = cropper.value.getCroppedCanvas({
    width: 300,
    height: 300,
  })

  canvas.toBlob(async (blob) => {
    const formData = new FormData()
    formData.append("avatar", blob, "avatar.jpg")

    try {
      const res = await api.post("/user/avatar", formData)

      currentUser.value.avatar = res.data.avatar
      localStorage.setItem("user", JSON.stringify(currentUser.value))

      preview.value = null

      cropper.value.destroy()
      cropper.value = null

    } catch (err) {
      console.error(err)
      alert("Errore durante upload avatar")
    } finally {
      uploading.value = false
    }
  }, "image/jpeg", 0.8)
}

/* ---------------- PROFILE EDIT ---------------- */
const editing = ref(false)

const form = ref({
  name: currentUser.value?.name || "",
  email: currentUser.value?.email || ""
})

const updateProfile = async () => {
  try {
    const res = await api.put("/user/profile", form.value)

    currentUser.value.name = res.data.name
    currentUser.value.email = res.data.email

    localStorage.setItem("user", JSON.stringify(currentUser.value))
    editing.value = false

  } catch (err) {
    console.error(err)
    alert("Errore aggiornamento profilo")
  }
}

/* ---------------- CLEANUP ---------------- */
onBeforeUnmount(() => {
  if (cropper.value) {
    cropper.value.destroy()
  }
})
</script>




<template>
  <MainLayout>
    <div class="max-w-xl mx-auto mt-12 bg-white p-8 rounded-3xl shadow-xl">

      <h1 class="text-2xl font-bold mb-6 text-gray-800">My Profile</h1>

      <!-- Avatar Section -->
      <div class="flex flex-col items-center gap-4">




        <!-- Preview con Cropper -->
        <div v-if="preview" class="w-64">
          <img ref="image" :src="preview" class="max-w-full rounded-lg" />
        </div>

         <div v-else class="relative group w-28 h-28">

  <!-- Avatar -->
  <img
    v-if="currentUser.avatar"
    :src="`http://127.0.0.1:8000/storage/${currentUser.avatar}`"
    class="w-28 h-28 rounded-full object-cover border-4 border-indigo-100 transition"
  />

  <!-- Placeholder -->
  <div
    v-else
    class="w-28 h-28 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-3xl font-bold"
  >
    {{ currentUser.name.charAt(0).toUpperCase() }}
  </div>

  <!-- Overlay -->
  <label
    class="absolute inset-0 bg-black bg-opacity-40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition cursor-pointer"
  >
    <span class="text-white text-sm">Edit</span>
    <input
      type="file"
      class="hidden"
      accept="image/jpeg,image/png,image/webp"
      @change="handleFileChange"
    />
  </label>

</div>




       

          <button
            v-if="preview"
            @click="uploadAvatar"
            :disabled="uploading"
            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition"
          >
            {{ uploading ? "Uploading..." : "Save Avatar" }}
          </button>
        </div>

      </div>

      <!-- User Info -->
      <div class="mt-10 space-y-4">
        <div class="bg-gray-50 p-4 rounded-xl">
          <p class="text-sm text-gray-500">Name</p>
          <p class="font-semibold">{{ currentUser.name }}</p>
        </div>

        <div class="bg-gray-50 p-4 rounded-xl">
          <p class="text-sm text-gray-500">Email</p>
          <p class="font-semibold">{{ currentUser.email }}</p>
        </div>
      </div>
     

     <div class="mt-10 space-y-4">

  <div class="flex justify-between items-center">
    <h2 class="text-lg font-semibold">Account Info</h2>
    <button
      @click="editing = !editing"
      class="text-indigo-600 hover:underline"
    >
      {{ editing ? "Cancel" : "Edit" }}
    </button>
  </div>

  <div class="bg-gray-50 p-4 rounded-xl">
    <p class="text-sm text-gray-500">Name</p>
    <input
      v-if="editing"
      v-model="form.name"
      class="w-full border rounded px-3 py-2"
    />
    <p v-else class="font-semibold">{{ currentUser.name }}</p>
  </div>

  <div class="bg-gray-50 p-4 rounded-xl">
    <p class="text-sm text-gray-500">Email</p>
    <input
      v-if="editing"
      v-model="form.email"
      class="w-full border rounded px-3 py-2"
    />
    <p v-else class="font-semibold">{{ currentUser.email }}</p>
  </div>

  <button
    v-if="editing"
    @click="updateProfile"
    class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700"
  >
    Save Changes
  </button>

</div>
  </MainLayout>
</template>