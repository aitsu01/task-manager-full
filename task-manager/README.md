# Task Manager API

Production-style REST API built with Laravel 12.

This project demonstrates:
- Token-based authentication (Sanctum)
- Protected API routes
- Policy-based authorization
- Form Request validation
- Route Model Binding
- Soft Deletes
- Eloquent relationships

---

##  Tech Stack

- Laravel 12
- PHP 8.4
- MySQL
- Laravel Sanctum
- Eloquent ORM

---

##  Database Architecture

### Roles
- admin
- manager
- user

### Users
- belongsTo Role
- hasMany Projects
- hasMany assigned Tasks

### Projects
- belongsTo User (owner)
- hasMany Tasks

### Tasks
- belongsTo Project
- belongsTo User (assigned_to)
- SoftDeletes enabled

---

##  Authentication (Sanctum)

### Login

POST `/api/login`

```json
{
  "email": "user@email.com",
  "password": "password"
}

Response:

{
  "token": "1|xxxxxxx",
  "user": { ... }
}
Authenticated Requests

Headers required:

Authorization: Bearer {token}
Accept: application/json
Logout

POST /api/logout

 Projects API (Protected)
List Projects

GET /api/projects

Returns only projects belonging to the authenticated user.

Create Project

POST /api/projects

{
  "name": "New Project",
  "description": "Project description"
}
Show Project

GET /api/projects/{id}

200 if owner

403 if not authorized

404 if not found

Update Project

PUT /api/projects/{id}

Delete Project

DELETE /api/projects/{id}

Uses Policy-based authorization.

 Authorization Logic

Authorization is handled using Laravel Policies.

Only the project owner can:

View

Update

Delete

Unauthorized access returns:

403 Forbidden
 Installation
git clone https://github.com/yourusername/task-manager.git
cd task-manager
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
 Current Status

Authentication complete

Project CRUD complete

Policy authorization implemented

API production-ready structure


## Nested Task CRUD

Implemented RESTful nested task routes:

GET    /api/projects/{project}/tasks  
POST   /api/projects/{project}/tasks  
GET    /api/projects/{project}/tasks/{task}  
PUT    /api/projects/{project}/tasks/{task}  
DELETE /api/projects/{project}/tasks/{task}  

Features:
- Sanctum authentication
- Project-based authorization (Policies)
- Validation with allowed status values (todo, doing, done)
- Pagination
- Nullable task assignment (assigned_to)
- JSON API Resource layer