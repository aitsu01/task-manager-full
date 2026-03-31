<script setup>
import { ref } from "vue"
import { useRouter } from "vue-router"
import api from "../services/api"

const name = ref("")
const email = ref("")
const password = ref("")
const passwordConfirmation = ref("")

const showPassword = ref(false)
const showConfirmPassword = ref(false)

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

    // reset campi
    name.value = ""
    email.value = ""
    password.value = ""
    passwordConfirmation.value = ""

  } catch (err) {
    error.value =
      err.response?.data?.message ||
      "Errore durante la registrazione."
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

        <!-- Nome -->
        <input
          v-model="name"
          placeholder="Nome"
          class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"
          required
        />

        <!-- Email -->
        <input
          v-model="email"
          type="email"
          placeholder="Email"
          class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"
          required
        />

        <!-- Password -->
        <div class="relative">
          <input
            v-model="password"
            :type="showPassword ? 'text' : 'password'"
            placeholder="Password"
            class="w-full px-3 py-2 border rounded-lg pr-10 focus:ring-2 focus:ring-indigo-500"
            required
          />
          <button
            type="button"
            @click="showPassword = !showPassword"
            class="absolute right-3 top-2 text-gray-500 text-sm"
          >
            {{ showPassword ? "Nascondi" : "Mostra" }}
          </button>
        </div>

        <!-- Conferma Password -->
        <div class="relative">
          <input
            v-model="passwordConfirmation"
            :type="showConfirmPassword ? 'text' : 'password'"
            placeholder="Conferma Password"
            class="w-full px-3 py-2 border rounded-lg pr-10 focus:ring-2 focus:ring-indigo-500"
            required
          />
          <button
            type="button"
            @click="showConfirmPassword = !showConfirmPassword"
            class="absolute right-3 top-2 text-gray-500 text-sm"
          >
            {{ showConfirmPassword ? "Nascondi" : "Mostra" }}
          </button>
        </div>

        <!-- Error -->
        <div v-if="error"
             class="bg-red-50 text-red-600 text-sm p-2 rounded">
          {{ error }}
        </div>

        <!-- Success -->
        <div v-if="message"
             class="bg-green-50 text-green-600 text-sm p-2 rounded">
          {{ message }}
        </div>

        <!-- Submit -->
        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition disabled:opacity-50"
        >
          {{ loading ? "Registrazione..." : "Registrati" }}
        </button>

      </form>

      <p class="text-sm text-center mt-6">
        Hai già un account?
        <router-link to="/login" class="text-indigo-600 hover:underline">
          Accedi
        </router-link>
      </p>

    </div>
  </div>
</template>