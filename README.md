# 🌱 Smart Irrigation Admin Panel

A Laravel-based admin dashboard for managing users, roles, permissions, and irrigation system settings.

---

## 🚀 Setup Instructions

### 1. Clone the Repository

```bash
git clone https://github.com/your-username/smart-irrigation-admin.git
cd smart-irrigation-admin
2. Install Dependencies
bash
Copy
Edit
composer install
npm install && npm run dev
3. Setup Environment
bash
Copy
Edit
cp .env.example .env
php artisan key:generate
4. Run Migrations & Seed Roles
bash
Copy
Edit
php artisan migrate
php artisan db:seed --class=RolesSeeder
🔐 Admin Authentication Flow
Superadmin Signup:
http://your-app-url/admin/signup

Admin & Technician Users
Can only be created by a Superadmin through the admin panel.

👥 User Roles
Role	Access Scope
Superadmin	Full system control
Admin	Manage users & roles
Technician	Access assigned operations
🧼 Cache Clearing Commands (Recommended)
bash
Copy
Edit
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
🔒 OTP Verification
OTP verification is enabled for enhanced login security for both users and admins.

📘 Built With
Laravel 11+

Spatie Laravel-Permission

Bootstrap 5

Font Awesome 6

Glassmorphism UI

🛠 Future Plans
Soft delete restoration interface

Activity logs

User analytics & usage reports

📫 Contact
For any questions or support, feel free to reach out:

Email: buildwithcode915@gmail.com
GitHub Issues: Open an issue

vbnet
Copy
Edit

Let me know if you'd like me to include `.env` examples or screenshots for GitHub preview too!
