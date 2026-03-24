This project was built to demonstrate:

Clean backend architecture
Real-world SaaS workflow
Authentication & role management
Full-stack integration
Professional repository structure





#  Task Manager SaaS (Full Stack)

Full Stack SaaS-style Task Management application built with:

-  Laravel 11 (API Backend)
-  Laravel Sanctum (Authentication)
-  Service Layer Architecture
-  Vue 3 + Vite (Frontend SPA)
-  TailwindCSS (UI)
-  MySQL Database

---

##  Features

###  Authentication
- Login / Logout
- Registration with approval workflow
- Token-based authentication (Sanctum)
- Route protection (SPA guards)
- Global 401 auto-redirect

---

###  Dashboard
- Total projects
- Total tasks
- Completion rate (%)
- Tasks per status (with percentages)
- Overdue tasks counter
- Weekly productivity trend (last 7 days)
- Recent tasks list

---

###  Admin Panel
- View all users
- Approve / Reject new registrations
- Assign roles (Admin / User / Manager)
- Update existing user roles
- Delete users
- Status badges (pending / approved / rejected)

---

###  Architecture

#### Backend
- RESTful API
- Service Layer (DashboardService, ProjectService, TaskService)
- Role-based access control
- Clean separation of concerns
- Eloquent relationships
- Soft-delete ready structure

#### Frontend
- Vue 3 Composition API
- Axios service abstraction
- Route guards
- SaaS-style layout with persistent sidebar
- Role-based UI rendering

---

##  Project Structure





task-manager-full/
│
├── task-manager-api/ # Laravel Backend
│
└── task-manager-ui/ # Vue 3 Frontend



---

##  Installation

### Backend

```bash
cd task-manager-api
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve






---

##  Installation

### Backend

```bash
cd task-manager-api
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve






