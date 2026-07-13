-- RM Manliquid Business System - Complete Database Schema
-- SQLite Database Import File
-- Fixed for Upload and Image Issues
-- Generated: April 2026

-- Enable foreign key constraints
PRAGMA foreign_keys = ON;

-- Drop existing tables if they exist (for clean import)
DROP TABLE IF EXISTS media_uploads;
DROP TABLE IF EXISTS eload_transactions;
DROP TABLE IF EXISTS eload_numbers;
DROP TABLE IF EXISTS eloads;
DROP TABLE IF EXISTS eload_categories;
DROP TABLE IF EXISTS attendance_emails;
DROP TABLE IF EXISTS contact_submissions;
DROP TABLE IF EXISTS attendances;
DROP TABLE IF EXISTS sales;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS sessions;
DROP TABLE IF EXISTS password_reset_tokens;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS jobs;
DROP TABLE IF EXISTS cache;

-- =============================================
-- SYSTEM TABLES
-- =============================================

-- Cache table for Laravel caching
CREATE TABLE cache (
    key TEXT PRIMARY KEY,
    value TEXT NOT NULL,
    expiration INTEGER NOT NULL
);

-- Jobs table for queue processing
CREATE TABLE jobs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    queue TEXT NOT NULL,
    payload TEXT NOT NULL,
    attempts INTEGER NOT NULL DEFAULT 0,
    reserved_at INTEGER NULL,
    available_at INTEGER NOT NULL,
    created_at INTEGER NOT NULL
);

-- =============================================
-- CORE USER MANAGEMENT
-- =============================================

-- Users table - Core user authentication and management
CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    phone TEXT NULL,
    address TEXT NULL,
    hire_date TEXT NULL,
    department TEXT NULL,
    employee_id TEXT UNIQUE NULL,
    employment_status TEXT DEFAULT 'active' CHECK (employment_status IN ('active', 'inactive', 'on_leave', 'terminated')),
    position TEXT NULL,
    salary REAL NULL,
    role TEXT DEFAULT 'employee' CHECK (role IN ('super_admin', 'admin', 'manager', 'cashier', 'employee')),
    access_enabled INTEGER DEFAULT 1,
    email_verified_at TEXT NULL,
    remember_token TEXT NULL,
    profile_image TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Password reset tokens
CREATE TABLE password_reset_tokens (
    email TEXT PRIMARY KEY,
    token TEXT NOT NULL,
    created_at TEXT NULL
);

-- Sessions table for user session management
CREATE TABLE sessions (
    id TEXT PRIMARY KEY,
    user_id INTEGER NULL,
    ip_address TEXT NULL,
    user_agent TEXT NULL,
    payload TEXT NOT NULL,
    last_activity INTEGER NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- =============================================
-- BUSINESS MANAGEMENT TABLES
-- =============================================

-- Products table - Inventory management
CREATE TABLE products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT NULL,
    price REAL NOT NULL,
    cost REAL NOT NULL,
    stock_quantity INTEGER DEFAULT 0,
    min_stock_level INTEGER DEFAULT 0,
    sku TEXT UNIQUE NOT NULL,
    category TEXT NULL,
    image_path TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Sales table - Sales transaction management
CREATE TABLE sales (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    total_amount REAL NOT NULL,
    payment_method TEXT NOT NULL,
    customer_name TEXT NULL,
    customer_email TEXT NULL,
    customer_phone TEXT NULL,
    items TEXT NOT NULL,
    user_id INTEGER NOT NULL,
    receipt_image TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Attendances table - Employee attendance tracking
CREATE TABLE attendances (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    date TEXT NOT NULL,
    check_in TEXT NULL,
    check_out TEXT NULL,
    location TEXT NULL,
    period TEXT NULL CHECK (period IN ('AM', 'PM')),
    check_in_image TEXT NULL,
    check_out_image TEXT NULL,
    deleted_at TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =============================================
-- E-LOAD SYSTEM TABLES
-- =============================================

-- E-load categories
CREATE TABLE eload_categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT NULL,
    status TEXT DEFAULT 'active' CHECK (status IN ('active', 'inactive')),
    image_path TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- E-load services
CREATE TABLE eloads (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    category_id INTEGER NOT NULL,
    name TEXT NOT NULL,
    price REAL NOT NULL,
    description TEXT NULL,
    status TEXT DEFAULT 'active' CHECK (status IN ('active', 'inactive')),
    image_path TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES eload_categories(id)
);

-- E-load numbers
CREATE TABLE eload_numbers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    eload_id INTEGER NOT NULL,
    number TEXT NOT NULL,
    status TEXT DEFAULT 'active' CHECK (status IN ('active', 'inactive')),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (eload_id) REFERENCES eloads(id)
);

-- E-load transactions
CREATE TABLE eload_transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    eload_number_id INTEGER NOT NULL,
    customer_name TEXT NOT NULL,
    customer_mobile TEXT NOT NULL,
    amount REAL NOT NULL,
    status TEXT DEFAULT 'pending' CHECK (status IN ('pending', 'completed', 'failed')),
    transaction_id TEXT NULL,
    receipt_image TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (eload_number_id) REFERENCES eload_numbers(id)
);

-- =============================================
-- COMMUNICATION TABLES
-- =============================================

-- Contact form submissions
CREATE TABLE contact_submissions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    company TEXT NOT NULL,
    email TEXT NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Attendance email notifications
CREATE TABLE attendance_emails (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    attendance_id INTEGER NOT NULL,
    email_sent INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (attendance_id) REFERENCES attendances(id)
);

-- =============================================
-- MEDIA/UPLOAD MANAGEMENT TABLE
-- =============================================

-- Media uploads table for image management
CREATE TABLE media_uploads (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    filename TEXT NOT NULL,
    original_name TEXT NOT NULL,
    mime_type TEXT NOT NULL,
    size INTEGER NOT NULL,
    path TEXT NOT NULL,
    url TEXT NULL,
    uploadable_type TEXT NULL,
    uploadable_id INTEGER NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- INDEXES FOR PERFORMANCE
-- =============================================

-- Users table indexes
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_employee_id ON users(employee_id);
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_users_department ON users(department);

-- Products table indexes
CREATE INDEX idx_products_sku ON products(sku);
CREATE INDEX idx_products_category ON products(category);

-- Sales table indexes
CREATE INDEX idx_sales_user_id ON sales(user_id);
CREATE INDEX idx_sales_created_at ON sales(created_at);
CREATE INDEX idx_sales_payment_method ON sales(payment_method);

-- Attendances table indexes
CREATE INDEX idx_attendances_user_id ON attendances(user_id);
CREATE INDEX idx_attendances_date ON attendances(date);
CREATE INDEX idx_attendances_check_in ON attendances(check_in);

-- E-load system indexes
CREATE INDEX idx_eloads_category_id ON eloads(category_id);
CREATE INDEX idx_eload_numbers_eload_id ON eload_numbers(eload_id);
CREATE INDEX idx_eload_transactions_eload_number_id ON eload_transactions(eload_number_id);
CREATE INDEX idx_eload_transactions_status ON eload_transactions(status);

-- Sessions table indexes
CREATE INDEX idx_sessions_user_id ON sessions(user_id);
CREATE INDEX idx_sessions_last_activity ON sessions(last_activity);

-- =============================================
-- TRIGGERS FOR AUTOMATIC TIMESTAMPS
-- =============================================

-- Update updated_at timestamp for users
CREATE TRIGGER update_users_updated_at 
    AFTER UPDATE ON users
    FOR EACH ROW
BEGIN
    UPDATE users SET updated_at = CURRENT_TIMESTAMP WHERE id = NEW.id;
END;

-- Update updated_at timestamp for products
CREATE TRIGGER update_products_updated_at 
    AFTER UPDATE ON products
    FOR EACH ROW
BEGIN
    UPDATE products SET updated_at = CURRENT_TIMESTAMP WHERE id = NEW.id;
END;

-- Update updated_at timestamp for sales
CREATE TRIGGER update_sales_updated_at 
    AFTER UPDATE ON sales
    FOR EACH ROW
BEGIN
    UPDATE sales SET updated_at = CURRENT_TIMESTAMP WHERE id = NEW.id;
END;

-- Update updated_at timestamp for attendances
CREATE TRIGGER update_attendances_updated_at 
    AFTER UPDATE ON attendances
    FOR EACH ROW
BEGIN
    UPDATE attendances SET updated_at = CURRENT_TIMESTAMP WHERE id = NEW.id;
END;

-- Update updated_at timestamp for eload_categories
CREATE TRIGGER update_eload_categories_updated_at 
    AFTER UPDATE ON eload_categories
    FOR EACH ROW
BEGIN
    UPDATE eload_categories SET updated_at = CURRENT_TIMESTAMP WHERE id = NEW.id;
END;

-- Update updated_at timestamp for eloads
CREATE TRIGGER update_eloads_updated_at 
    AFTER UPDATE ON eloads
    FOR EACH ROW
BEGIN
    UPDATE eloads SET updated_at = CURRENT_TIMESTAMP WHERE id = NEW.id;
END;

-- Update updated_at timestamp for eload_numbers
CREATE TRIGGER update_eload_numbers_updated_at 
    AFTER UPDATE ON eload_numbers
    FOR EACH ROW
BEGIN
    UPDATE eload_numbers SET updated_at = CURRENT_TIMESTAMP WHERE id = NEW.id;
END;

-- Update updated_at timestamp for eload_transactions
CREATE TRIGGER update_eload_transactions_updated_at 
    AFTER UPDATE ON eload_transactions
    FOR EACH ROW
BEGIN
    UPDATE eload_transactions SET updated_at = CURRENT_TIMESTAMP WHERE id = NEW.id;
END;

-- Update updated_at timestamp for contact_submissions
CREATE TRIGGER update_contact_submissions_updated_at 
    AFTER UPDATE ON contact_submissions
    FOR EACH ROW
BEGIN
    UPDATE contact_submissions SET updated_at = CURRENT_TIMESTAMP WHERE id = NEW.id;
END;

-- Update updated_at timestamp for attendance_emails
CREATE TRIGGER update_attendance_emails_updated_at 
    AFTER UPDATE ON attendance_emails
    FOR EACH ROW
BEGIN
    UPDATE attendance_emails SET updated_at = CURRENT_TIMESTAMP WHERE id = NEW.id;
END;

-- =============================================
-- SAMPLE DATA (Optional - for testing)
-- =============================================

-- Insert default super admin user (password: password)
INSERT INTO users (name, email, password, role, access_enabled, employee_id, department, position, employment_status) 
VALUES ('Super Admin', 'admin@manliquid.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin', 1, 'SA001', 'IT', 'Super Administrator', 'active');

-- Insert sample e-load categories
INSERT INTO eload_categories (name, description, status) VALUES 
('Mobile Load', 'Mobile phone load services', 'active'),
('Internet Load', 'Internet data load services', 'active'),
('Gaming Load', 'Gaming credits and points', 'active');

-- Insert sample products
INSERT INTO products (name, description, price, cost, stock_quantity, min_stock_level, sku, category) VALUES 
('Sample Product 1', 'Description for sample product 1', 100.00, 50.00, 50, 10, 'SP001', 'General'),
('Sample Product 2', 'Description for sample product 2', 200.00, 100.00, 30, 5, 'SP002', 'General');

-- =============================================
-- IMPORT INSTRUCTIONS
-- =============================================

/*
To import this database schema:

1. Using SQLite Command Line:
   sqlite3 database/database.sqlite < database_schema.sql

2. Using PHP (create import script):
   <?php
   $pdo = new PDO('sqlite:database/database.sqlite');
   $sql = file_get_contents('database_schema.sql');
   $pdo->exec($sql);
   ?>

3. Using SQLite GUI Tools:
   - Open DB Browser for SQLite
   - File > Import > Database from SQL file
   - Select database_schema.sql

4. For Laravel Migration:
   - This schema is compatible with Laravel migrations
   - Run: php artisan migrate:fresh --seed

Default Login Credentials:
- Email: admin@manliquid.com
- Password: password

Remember to change the default password in production!
*/
