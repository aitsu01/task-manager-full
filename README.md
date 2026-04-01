#  Task Manager SaaS

A modern collaborative Task Manager built with Laravel and Vue 3, designed as a SaaS-style application with role-based access control and real-world project workflows.

---

##  Overview

This project is a full-stack SaaS application that allows teams to:

* Create and manage projects
* Collaborate with team members
* Assign and track tasks
* Control access via roles and permissions
* Monitor progress through dashboards

It was developed as a **portfolio project for a Laravel/Vue internship**, focusing on clean architecture and real-world patterns.

---

##  Tech Stack

### Backend

* PHP (Laravel 12)
* MySQL
* REST API
* Laravel Sanctum (authentication)
* Policy-based authorization
* Service Layer architecture

### Frontend

* Vue 3 (Composition API)
* Vue Router
* Axios
* Tailwind CSS

---

##  Authentication & Authorization

* Token-based authentication using Laravel Sanctum
* Role-based access control:

### Global Roles

* `admin`
* `user`

### Project Roles

* `owner`
* `manager`
* `member`

---

##  Projects

* Each project has a creator (owner)
* Users can be invited as members
* Roles are stored in a pivot table (`project_user`)

### Permissions

| Action                | Who can do it |
| --------------------- | ------------- |
| View project          | Members       |
| Update/Delete project | Owner         |
| Manage members        | Owner / Admin |

---

##  Tasks

* Tasks belong to a project
* Can be assigned to users
* Support lifecycle:

```
todo → doing → done
```

### Task Workflow

* All members can create tasks
* Only **owner/admin** can:

  * Approve tasks
  * Change status
  * Delete tasks

---

##  Dashboard

Includes:

* Total projects
* Total tasks
* Completion rate
* Tasks per status
* Overdue tasks
* Weekly completed tasks

---

##  Project Members

* Add members via email
* Assign roles (manager/member)
* Remove members
* Owner cannot be removed

---

##  Key Features

* SaaS-style UI
* Role-based permissions (backend + frontend)
* Task approval workflow
* Real-time UI updates (optimistic updates)
* Error handling and validation
* Clean separation of concerns (Service Layer)

---

##  API Endpoints (Examples)

```
GET    /projects
POST   /projects
GET    /projects/{id}
GET    /projects/{id}/tasks
POST   /projects/{id}/tasks
PATCH  /projects/{id}/tasks/{task}
PATCH  /projects/{id}/tasks/{task}/status
GET    /projects/{id}/members
POST   /projects/{id}/members
```

---

##  Installation

### Backend

```bash
git clone https://github.com/aitsu01/task-manager-full
cd task-manager
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

### Frontend

```bash
cd task-manager-ui
npm install
npm run dev
```

---

##  Future Improvements

* Kanban board (drag & drop)
* Notifications system
* Task comments
* Activity logs
* Advanced filtering
* Per-project dashboards

---

##  Author

Developed by **aitsu01** as a portfolio project for a Laravel/Vue internship.

---

##  Notes

This project was built with a focus on:

* Clean architecture
* Scalability
* Real-world SaaS patterns

---






