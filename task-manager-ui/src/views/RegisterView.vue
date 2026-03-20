<script setup>
import { ref } from "vue"
import { useRouter } from "vue-router"
import api from "../services/api"

const name = ref("")
const email = ref("")
const password = ref("")
const passwordConfirmation = ref("")
const loading = ref(false)
const message = ref(null)
const error = ref(null)

const router = useRouter()

const register = async () => {
  error.value = null
  message.value = null
  loading.value = true

  try {
    await api.post("/register", {
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value
    })

    message.value = "Registrazione completata. Attendi approvazione."
  } catch (err) {
    error.value = err.response?.data?.message || "Errore registrazione"
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-10 rounded-2xl shadow-xl w-96">

      <h2 class="text-2xl font-semibold text-center mb-2">
        Registrazione
      </h2>

      <p class="text-sm text-gray-500 text-center mb-8">
        Crea un account (richiede approvazione)
      </p>

      <form @submit.prevent="register" class="space-y-4">

        <input v-model="name" placeholder="Nome"
          class="w-full px-3 py-2 border rounded-lg" required />

        <input v-model="email" type="email" placeholder="Email"
          class="w-full px-3 py-2 border rounded-lg" required />

        <input v-model="password" type="password" placeholder="Password"
          class="w-full px-3 py-2 border rounded-lg" required />

        <input v-model="passwordConfirmation"
          type="password"
          placeholder="Conferma Password"
          class="w-full px-3 py-2 border rounded-lg"
          required />

        <div v-if="error"
             class="bg-red-50 text-red-600 text-sm p-2 rounded">
          {{ error }}
        </div>

        <div v-if="message"
             class="bg-green-50 text-green-600 text-sm p-2 rounded">
          {{ message }}
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition disabled:opacity-50"
        >
          {{ loading ? "Registrazione..." : "Registrati" }}
        </button>

      </form>

      <p class="text-sm text-center mt-6">
        Hai già un account?
        <router-link to="/login" class="text-blue-600 hover:underline">
          Accedi
        </router-link>
      </p>

    </div>
  </div>
</template>