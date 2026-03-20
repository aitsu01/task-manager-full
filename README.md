#  Task Manager SaaS (Laravel + Vue)

Fullstack Task Management System built with:

-  Laravel 11 (REST API)
-  Vue 3 + Vite (Dashboard UI)
-  Role-based access (Admin / Manager / User)
-  Real-time Dashboard Analytics
-  Token Authentication (Sanctum)
-  SaaS-ready architecture (Monorepo)

---

#  Project Structure

#  Task Manager SaaS (Laravel + Vue)

Fullstack Task Management System built with:

-  Laravel 11 (REST API)
-  Vue 3 + Vite (Dashboard UI)
-  Role-based access (Admin / Manager / User)
-  Real-time Dashboard Analytics
-  Token Authentication (Sanctum)
-  SaaS-ready architecture (Monorepo)

---

#  Project Structure


---

#  Backend (Laravel API)

## Features

✅ Authentication (Login / Token-based)  
✅ Role-based authorization (Admin, Manager, User)  
✅ Project CRUD  
✅ Nested Task CRUD (`/projects/{id}/tasks`)  
✅ Soft Deletes  
✅ Dynamic filtering (status, due_date)  
✅ Pagination  
✅ API Resources  
✅ Policies for access control  

---

## 📊 Advanced Dashboard Endpoint

`GET /api/dashboard`

Returns:

- Total Projects
- Total Tasks
- Completion Rate (%)
- Tasks per Status (count + percentage)
- Overdue Tasks
- Weekly Productivity Trend (last 7 days)
- Recent Tasks

Example response:

```json
{
  "total_projects": 8,
  "total_tasks": 32,
  "completion_rate": 37.5,
  "tasks_per_status": {
    "todo": { "count": 8, "percentage": 25 },
    "doing": { "count": 12, "percentage": 37.5 },
    "done": { "count": 12, "percentage": 37.5 }
  },
  "overdue_tasks": 18
}


Frontend (Vue 3 + Vite)
Features

✅ Login via API
✅ Token storage
✅ Dashboard page
✅ Live API integration
✅ Tailwind CSS styling
✅ Weekly productivity visualization



Authentication

Token-based authentication using Laravel Sanctum.

Example login:

POST /api/login

Returns:

{
  "token": "...",
  "user": { ... }
}

Use token in headers:

Authorization: Bearer YOUR_TOKEN
Accept: application/json
🗄 Database Structure
Users

id

role_id

name

email

Roles

admin

manager

user

Projects

id

name

description

user_id

Tasks

id

title

description

status (todo, doing, done)

due_date

assigned_to

project_id

soft deletes

 SaaS Concepts Implemented

Multi-user architecture

Role-based visibility

Nested resources

Aggregated analytics

Percentage metrics

Weekly trend calculations

Clean API design

Monorepo architecture

 How To Run
Backend
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
Frontend
cd frontend
npm install
npm run dev
 Next Improvements (Roadmap)

 Project progress % tracking

 Kanban board UI

 Assign users to tasks

 Email notifications

 Docker setup

 Production deployment

 CI/CD pipeline




---



