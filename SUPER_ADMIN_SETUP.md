# 🔑 Super Admin Setup

## Super Admin Credentials

The Super Admin account has been created with the following credentials:

- **Email:** `superadmin@example.com`
- **Password:** `SuperSecret123!`

## Sign-In Flow

1. Navigate to the **Sign in** page at `/login`
2. Enter the Super Admin credentials above
3. Click **Sign In**
4. You will be automatically redirected to the **Super Admin Dashboard** at `/dashboard/super-admin`

## Super Admin Dashboard Features

The Super Admin has access to the following full-system capabilities:

### 👥 User Management
- Create, update, and delete Admin and Employee accounts
- View all user accounts and roles

### 📦 Products & Inventory
- Add, update, and delete products
- Monitor inventory levels and stock alerts
- Track stock movements

### 💼 Sales & Transactions
- Process sales transactions
- Auto-deduct stock after purchase
- Generate printable receipts

### 📊 Reports & Analytics
- View daily, weekly, and monthly sales reports
- Access complete sales history
- Monitor employee and Admin Assistant attendance

### 📋 Attendance Tracking
- View employee attendance records
- Track Admin Assistant attendance

## Role-Based Access Control

The system uses three roles:

- **super_admin**: Full system access (can manage users, products, sales, reports, attendance)
- **admin**: Manage inventory and sales, view reports
- **user**: View personal dashboard and sales data

## Database Seeding

To re-seed the Super Admin account:

```bash
php artisan db:seed --class=SuperAdminSeeder
```

To reset and seed all demo data:

```bash
php artisan migrate:fresh --seed
```

## Route Protection

The `/dashboard/super-admin` route is protected by:
- `auth` middleware (user must be logged in)
- `RoleMiddleware:super_admin` (user must have super_admin role)

Any attempt to access without proper role will result in a 403 Forbidden error.
