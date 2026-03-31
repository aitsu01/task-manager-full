<script setup>
import { ref } from "vue"
import { useRoute, useRouter } from "vue-router"
import api from "../services/api"

const route = useRoute()
const router = useRouter()

const token = route.query.token
const email = route.query.email

const password = ref("")
const password_confirmation = ref("")
const loading = ref(false)
const message = ref(null)
const error = ref(null)

const resetPassword = async () => {
  error.value = null
  message.value = null
  loading.value = true

  try {
    await api.post("/reset-password", {
      token,
      email,
      password: password.value,
      password_confirmation: password_confirmation.value
    })

    message.value = "Password aggiornata con successo!"

    setTimeout(() => {
      router.push("/")
    }, 2000)

  } catch (err) {
    error.value = "Token non valido o password errata."
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-10 rounded-2xl shadow-xl w-96">

      <h2 class="text-2xl font-semibold text-center mb-6">
        Nuova Password
      </h2>

      <form @submit.prevent="resetPassword" class="space-y-5">

        <div>
          <label class="block text-sm mb-1 text-gray-600">
            Nuova Password
          </label>
          <input
            v-model="password"
            type="password"
            required
            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <div>
          <label class="block text-sm mb-1 text-gray-600">
            Conferma Password
          </label>
          <input
            v-model="password_confirmation"
            type="password"
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
          {{ loading ? "Aggiornamento..." : "Reset Password" }}
        </button>

      </form>

    </div>
  </div>
</template>