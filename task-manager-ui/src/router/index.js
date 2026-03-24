import { createRouter, createWebHistory } from "vue-router"
import Dashboard from "../views/Dashboard.vue"
import LoginView from "../views/LoginView.vue"
import RegisterView from "../views/RegisterView.vue"
import AdminUsersView from "../views/AdminUsersView.vue"




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

{
  path: "/admin/users",
  component: AdminUsersView
},

{
  path: "/projects/:id/members",
  name: "ProjectMembers",
  component: () => import("../views/ProjectMembersView.vue")
},

{
  path: "/projects",
  name: "Projects",
  component: () => import("../views/ProjectsView.vue")
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

  if (to.path.startsWith("/admin") && !localStorage.getItem("token")) {
  return "/login"
}


})

export default router