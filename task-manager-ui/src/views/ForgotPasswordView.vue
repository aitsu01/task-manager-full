<script setup>
import { ref } from "vue"
import { useRouter } from "vue-router"
import api from "../services/api"

const router = useRouter()

const email = ref("")
const loading = ref(false)
const message = ref(null)
const error = ref(null)

const sendResetLink = async () => {
  error.value = null
  message.value = null
  loading.value = true

  try {
    await api.post("/forgot-password", {
      email: email.value
    })

    message.value = "Se l'email esiste, riceverai un link per il reset."

    // redirect dopo 3 secondi
    setTimeout(() => {
      router.push("/")
    }, 3000)

  } catch (err) {
    error.value = "Errore durante l'invio."
  } finally {
    loading.value = false
  }
}

const goToLogin = () => {
  router.push("/")
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">

    <div class="bg-white p-10 rounded-2xl shadow-xl w-96">

      <h2 class="text-2xl font-semibold text-center mb-6">
        Recupera Password
      </h2>

      <form @submit.prevent="sendResetLink" class="space-y-5">

        <!-- Email -->
        <div>
          <label class="block text-sm mb-1 text-gray-600">Email</label>
          <input
            v-model="email"
            type="email"
            required
            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"
          />
        </div>

        <!-- Success -->
        <div v-if="message"
             class="bg-green-50 text-green-600 text-sm p-2 rounded">
          {{ message }}
        </div>

        <!-- Error -->
        <div v-if="error"
             class="bg-red-50 text-red-600 text-sm p-2 rounded">
          {{ error }}
        </div>

        <!-- Submit -->
        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition disabled:opacity-50"
        >
          {{ loading ? "Invio..." : "Invia link reset" }}
        </button>

        <!-- Back to Login -->
        <button
          type="button"
          @click="goToLogin"
          class="w-full border border-gray-300 py-2 rounded-lg hover:bg-gray-100 transition"
        >
          Ritorna al Login
        </button>

      </form>

    </div>

  </div>
</template>