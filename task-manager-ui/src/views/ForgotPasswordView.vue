<script setup>
import { ref } from "vue"
import api from "../services/api"

const email = ref("")
const loading = ref(false)
const message = ref(null)
const error = ref(null)

const sendResetLink = async () => {
  error.value = null
  message.value = null
  loading.value = true

  try {
    const res = await api.post("/forgot-password", {
      email: email.value
    })



    message.value = "Ti abbiamo inviato una email per il reset."
  } catch (err) {
    error.value = "Errore invio email."
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-10 rounded-2xl shadow-xl w-96">

      <h2 class="text-2xl font-semibold text-center mb-6">
        Recupera Password
      </h2>

      <form @submit.prevent="sendResetLink" class="space-y-5">

        <div>
          <label class="block text-sm mb-1 text-gray-600">Email</label>
          <input
            v-model="email"
            type="email"
            required
            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <div v-if="message"
             class="bg-green-50 text-green-600 text-sm p-2 rounded">
          {{ message }}
        </div>

        <div v-if="error"
             class="bg-red-50 text-red-600 text-sm p-2 rounded">
          {{ error }}
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition"
        >
          {{ loading ? "Invio..." : "Invia link reset" }}
        </button>

        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-red-600 text-white py-2 rounded-lg hover:bg-blue-700 transition"
        >
          {{ loading ? "Invio..." : "Ritorna al Login" }}
        </button>



      </form>

    </div>
  </div>
</template>