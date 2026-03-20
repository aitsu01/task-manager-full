<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-8 rounded-xl shadow w-96">
      <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>

      <form @submit.prevent="login">
        <input
          v-model="email"
          type="email"
          placeholder="Email"
          class="w-full mb-4 p-2 border rounded"
        />

        <input
          v-model="password"
          type="password"
          placeholder="Password"
          class="w-full mb-4 p-2 border rounded"
        />

        <button
          type="submit"
          class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700"
        >
          Login
        </button>

        <p v-if="error" class="text-red-500 mt-4 text-sm">
          {{ error }}
        </p>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue"
import { useRouter } from "vue-router"
import api from "../services/api"

const email = ref("")
const password = ref("")
const error = ref(null)
const router = useRouter()

const login = async () => {
  try {
    const response = await api.post("/login", {
      email: email.value,
      password: password.value
    })

    localStorage.setItem("token", response.data.token)

    router.push("/dashboard")
  } catch (err) {
    error.value = "Credenziali non valide"
  }
}
</script>