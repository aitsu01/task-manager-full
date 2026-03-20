import { createRouter, createWebHistory } from "vue-router"
import Dashboard from "../views/Dashboard.vue"
import LoginView from "../views/LoginView.vue"
import RegisterView from "../views/RegisterView.vue"


const routes = [
  {
    path: "/",
    redirect: "/login",
  },
  {
    path: "/login",
    component: LoginView,
  },
  {
    path: "/dashboard",
    component: Dashboard,
    meta: { requiresAuth: true },
  },
 

{
  path: "/register",
  component: RegisterView
},
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to) => {
  const token = localStorage.getItem("token")

  if (to.meta.requiresAuth && !token) {
    return "/login"
  }

  if (to.path === "/login" && token) {
    return "/dashboard"
  }
})

export default router