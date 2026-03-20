<script setup>
import { ref } from "vue"
import { useRouter } from "vue-router"
import api from "../services/api"

const email = ref("")
const password = ref("")
const showPassword = ref(false)
const loading = ref(false)
const error = ref(null)

const router = useRouter()

const login = async () => {
  error.value = null
  loading.value = true

  try {
    const response = await api.post("/login", {
      email: email.value,
      password: password.value
    })

    localStorage.setItem("token", response.data.token)
    router.push("/dashboard")

  } catch (err) {
    error.value = "Email o password non validi"
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">

    <div class="bg-white p-10 rounded-2xl shadow-xl w-96">

      <h2 class="text-2xl font-semibold text-center mb-2">
        Task Manager
      </h2>

      <p class="text-sm text-gray-500 text-center mb-8">
        Accedi alla tua dashboard
      </p>

      <form @submit.prevent="login" class="space-y-5">

        <!-- Email -->
        <div>
          <label class="block text-sm mb-1 text-gray-600">Email</label>
          <input
            v-model="email"
            type="email"
            required
            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <!-- Password -->
        <div>
          <label class="block text-sm mb-1 text-gray-600">Password</label>

          <div class="relative">
            <input
              v-model="password"
              :type="showPassword ? 'text' : 'password'"
              required
              class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10"
            />

            <!-- Toggle -->
            <button
              type="button"
              @click="showPassword = !showPassword"
              class="absolute right-3 top-2 text-gray-500 text-sm"
            >
              {{ showPassword ? "Nascondi" : "Mostra" }}
            </button>
          </div>
        </div>

        <!-- Error -->
        <div v-if="error"
             class="bg-red-50 text-red-600 text-sm p-2 rounded">
          {{ error }}
        </div>

        <!-- Button -->
        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition disabled:opacity-50"
        >
          <span v-if="!loading">Accedi</span>
          <span v-else>Accesso in corso...</span>
        </button>

      </form>
      <p class="text-sm text-center mt-6">
  Non hai un account?
  <router-link to="/register" class="text-blue-600 hover:underline">
    Registrati
  </router-link>
</p>

    </div>

  </div>
</template>