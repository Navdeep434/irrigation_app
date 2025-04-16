# 🌱 Smart Irrigation Admin Panel

[![Laravel](https://img.shields.io/badge/Laravel-11+-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![Spatie Permission](https://img.shields.io/badge/Spatie-Permission-38c172?style=flat-square&logo=laravel&logoColor=white)](https://github.com/spatie/laravel-permission)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?style=flat-square&logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![Font Awesome](https://img.shields.io/badge/Font_Awesome-6-528DD7?style=flat-square&logo=fontawesome&logoColor=white)](https://fontawesome.com)

A Laravel-based admin dashboard for managing users, roles, permissions, and irrigation system settings.

---

## 🚀 Setup Instructions

### 1. Clone the Repository

```bash
git clone https://github.com/your-username/smart-irrigation-admin.git
cd smart-irrigation-admin
```

### 2. Install Dependencies

```bash
composer install
npm install && npm run dev
```

### 3. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Run Migrations & Seed Roles

```bash
php artisan migrate
php artisan db:seed --class=RolesSeeder
```

## 🔐 Admin Authentication Flow

**Superadmin Signup:**
http://your-app-url/admin/signup

**Admin & Technician Users**
Can only be created by a Superadmin through the admin panel.

## 👥 User Roles

| Role | Access Scope |
|------|-------------|
| **Superadmin** | Full system control |
| **Admin** | Manage users & roles |
| **Technician** | Access assigned operations |

## 🧼 Cache Clearing Commands (Recommended)

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## 🔒 OTP Verification

OTP verification is enabled for enhanced login security for both users and admins.

## 📋 Features

- 👤 Complete user management system
- 🔒 Role-based permissions with Spatie
- 🚰 Irrigation system configuration
- 🔐 Enhanced security with OTP verification
- 🎨 Modern Glassmorphism UI design

## 📘 Built With

- **Laravel 11+**
- **Spatie Laravel-Permission**
- **Bootstrap 5**
- **Font Awesome 6**
- **Glassmorphism UI**

## 🛠 Future Plans

- Soft delete restoration interface
- Activity logs
- User analytics & usage reports

## 📫 Contact

For any questions or support, feel free to reach out:

- 📧 **Email:** buildwithcode915@gmail.com
- 🐞 **GitHub Issues:** [Open an issue](https://github.com/your-username/smart-irrigation-admin/issues)

---

<p align="center">
  Made with 💧 and ☀️ for smarter irrigation
</p>