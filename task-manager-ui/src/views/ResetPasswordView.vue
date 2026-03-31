<script setup>
import { ref, computed } from "vue"
import { useRoute, useRouter } from "vue-router"
import api from "../services/api"

const route = useRoute()
const router = useRouter()

const token = route.query.token
const email = route.query.email

const password = ref("")
const passwordConfirmation = ref("")

const showPassword = ref(false)
const showConfirmPassword = ref(false)

const loading = ref(false)
const message = ref(null)
const error = ref(null)

/* -------- Password Match Check -------- */
const passwordsMatch = computed(() => {
  return password.value === passwordConfirmation.value
})

/* -------- Reset Function -------- */
const resetPassword = async () => {
  error.value = null
  message.value = null

  if (!passwordsMatch.value) {
    error.value = "Le password non coincidono."
    return
  }

  loading.value = true

  try {
    await api.post("/reset-password", {
      token,
      email,
      password: password.value,
      password_confirmation: passwordConfirmation.value
    })

    message.value = "Password aggiornata con successo!"

    setTimeout(() => {
      router.push("/login")
    }, 3000)

  } catch (err) {
    error.value = "Token non valido o scaduto."
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-10 rounded-2xl shadow-xl w-96">

      <h2 class="text-2xl font-semibold text-center mb-6">
        Imposta Nuova Password
      </h2>

      <form @submit.prevent="resetPassword" class="space-y-5">

        <!-- Nuova Password -->
        <div class="relative">
          <label class="block text-sm mb-1 text-gray-600">
            Nuova Password
          </label>

          <input
            v-model="password"
            :type="showPassword ? 'text' : 'password'"
            required
            class="w-full px-3 py-2 border rounded-lg pr-10 focus:ring-2 focus:ring-indigo-500"
          />

          <button
            type="button"
            @click="showPassword = !showPassword"
            class="absolute right-3 top-8 text-gray-500 text-sm"
          >
            {{ showPassword ? "Nascondi" : "Mostra" }}
          </button>
        </div>

        <!-- Conferma Password -->
        <div class="relative">
          <label class="block text-sm mb-1 text-gray-600">
            Conferma Password
          </label>

          <input
            v-model="passwordConfirmation"
            :type="showConfirmPassword ? 'text' : 'password'"
            required
            class="w-full px-3 py-2 border rounded-lg pr-10 focus:ring-2 focus:ring-indigo-500"
          />

          <button
            type="button"
            @click="showConfirmPassword = !showConfirmPassword"
            class="absolute right-3 top-8 text-gray-500 text-sm"
          >
            {{ showConfirmPassword ? "Nascondi" : "Mostra" }}
          </button>
        </div>

        <!-- Password match live hint -->
        <div
          v-if="password && passwordConfirmation"
          class="text-sm"
          :class="passwordsMatch ? 'text-green-600' : 'text-red-600'"
        >
          {{ passwordsMatch ? "Le password coincidono ✓" : "Le password non coincidono" }}
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
          {{ loading ? "Aggiornamento..." : "Reset Password" }}
        </button>

      </form>

    </div>
  </div>
</template>