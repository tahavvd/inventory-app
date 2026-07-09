<div align="center">

# 📦 RivoCode — Inventory Management System

![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Filament](https://img.shields.io/badge/Filament-4-FDAE4B?style=for-the-badge&logo=filament&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.5-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.4-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Sail-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

**A clean, role-based inventory management system built for small businesses.**  
Track stock, manage orders, handle suppliers and warehouses — all in one place.

[Features](#-features) • [Tech Stack](#-tech-stack) • [Installation](#-installation) • [Usage Guide](#-usage-guide) • [Roles](#-roles)

</div>

---

## ✨ Features

### 📊 Inventory & Stock
- Real-time inventory tracking per product per warehouse
- Automatic stock deduction on every order
- Automatic stock restoration on order cancellation
- Race condition protection via database transactions and row locking
- Full stock transaction history (In / Out) with filters

### 🛒 Orders
- Create orders with multiple products and quantities
- Prevents ordering more than available stock (server-side validation)
- Order status flow: `Pending → Processing → Completed`
- Cancel orders at any stage (except Completed) — stock is automatically reversed
- Full status change history with timestamps and responsible user
- Edit protection — only Pending orders can be modified

### 🏭 Supplies
- Record incoming stock from suppliers
- Automatically increments inventory on creation
- Linked to specific warehouse and supplier

### 🔐 Role-Based Access Control
- **Admin** — full access to everything including user management
- **Employee** — access to operations only, cannot see or manage users

### 🗂️ Resource Management
- Products with units (Piece, Box, Kg, Liter, Meter)
- Multiple Warehouses
- Suppliers
- Customers
- Employees (Users)

---

## 🛠 Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 12 |
| Admin Panel | Filament 4 |
| Frontend | Livewire, Alpine.js, Tailwind CSS |
| Database | MySQL 8.4 |
| Local Dev | Laravel Sail (Docker) |
| Language | PHP 8.5 |

---

## 🚀 Installation

### Prerequisites

Before you start, make sure you have the following installed:

| Tool | Windows | Mac | Linux |
|---|---|---|---|
| Docker Desktop | [Download](https://www.docker.com/products/docker-desktop/) | [Download](https://www.docker.com/products/docker-desktop/) | [Install Docker Engine](https://docs.docker.com/engine/install/) |
| WSL2 | Required on Windows | Not needed | Not needed |
| Git | [Download](https://git-scm.com/) | [Download](https://git-scm.com/) | Usually pre-installed |

> **Windows users:** Make sure WSL2 is enabled and Docker Desktop is set to use the WSL2 backend before continuing. All commands below must be run inside your **WSL2 terminal**, not PowerShell or CMD.

---

### Step 1 — Clone the Repository

```bash
git clone https://github.com/yourname/rivocode.git
cd rivocode
```

---

### Step 2 — Copy the Environment File

```bash
cp .env.example .env
```

Open `.env` and update the database credentials if needed. The defaults work out of the box with Sail.

---

### Step 3 — Install PHP Dependencies

Since you may not have PHP installed locally, use this Docker one-liner to run Composer inside a temporary container:

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php85-composer:latest \
    composer install --ignore-platform-reqs
```

> This pulls a temporary PHP + Composer container, installs dependencies, then removes itself. Laravel Sail is now installed as part of this step.

---

### Step 4 — Start the Containers

```bash
./vendor/bin/sail up -d
```

This starts:
- **PHP 8.5** application container
- **MySQL 8.4** database container
- **Adminer** database GUI at `http://localhost:8080`

> First run takes a few minutes to pull the Docker images. Subsequent starts are instant.

---

### Step 5 — Generate Application Key

```bash
./vendor/bin/sail artisan key:generate
```

---

### Step 6 — Run Migrations and Seed the Database

```bash
./vendor/bin/sail artisan migrate --seed
```

This creates all database tables and seeds the default admin account.

---

### Step 7 — Install and Build Frontend Assets

```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

---

### Step 8 — Open the App

Visit **[http://localhost/admin](http://localhost/admin)** in your browser.

Log in with the default admin credentials:

| Field | Value |
|---|---|
| Email | `admin@gmail.com` |
| Password | `password` |

> ⚠️ **Change your password immediately after first login.**

---

### Useful Sail Commands

```bash
# Start containers
./vendor/bin/sail up -d

# Stop containers
./vendor/bin/sail down

# Run Artisan commands
./vendor/bin/sail artisan <command>

# Open MySQL shell
./vendor/bin/sail mysql

# View logs
./vendor/bin/sail logs
```

> **Tip:** Add `alias sail='./vendor/bin/sail'` to your `~/.bashrc` or `~/.zshrc` so you can just type `sail up -d` instead of the full path.

---

## 📖 Usage Guide

### 🔑 Logging In

Navigate to `http://localhost/admin` and log in with your credentials. Admins see the full sidebar. Employees see everything except the Users resource.

---

### 📦 Setting Up Your Inventory (Start Here)

Before creating orders, set up your base data in this order:

1. **Warehouses** — Add the physical locations where stock is stored
2. **Suppliers** — Add your stock providers
3. **Customers** — Add your clients
4. **Products** — Add products with their unit type (Piece, Box, Kg, etc.)
5. **Supplies** — Record initial stock coming in from suppliers → this fills your inventory

---

### 🏭 Recording a Supply (Stock In)

A supply represents stock arriving from a supplier into a warehouse.

1. Go to **Supplies** → **New Supply**
2. Select the **Supplier**, **Warehouse**, and **Product**
3. Enter the **Quantity** received
4. Save — inventory is automatically updated

Every supply creates a **Stock Transaction (In)** which you can view in the Stock Transactions resource.

---

### 🛒 Creating an Order (Stock Out)

An order represents stock leaving a warehouse to a customer.

1. Go to **Orders** → **New Order**
2. Select the **Customer**
3. Add **Order Items** — select product, warehouse, and quantity
   - The system **blocks** you from ordering more than what's available
4. Save — inventory is automatically decremented per item

---

### 📋 Managing Order Status

Orders follow this lifecycle:

```
Pending → Processing → Completed
                ↓
            Cancelled (available at any stage except Completed)
```

From the **Orders table**, use the action buttons on each row:

| Button | What It Does |
|---|---|
| `Mark Processing` | Moves order from Pending to Processing |
| `Mark Completed` | Moves order from Processing to Completed |
| `Cancel Order` | Cancels the order and **automatically restores stock** |

> Cancelling an order is safe — all stock taken by that order is returned to inventory automatically.

---

### 👁️ Viewing an Order

Click **View** on any order to see:
- Full order details and total
- All ordered items with quantities and prices
- Complete status history — who changed what and when

---

### ✏️ Editing an Order

Only **Pending** orders can be edited. Once an order moves to Processing or Completed, editing is locked. Attempting to access the edit page directly via URL will redirect you to the view page.

---

### 📊 Stock Transactions

The **Stock Transactions** resource is your audit trail — every stock movement is recorded here automatically. You cannot create, edit, or delete transactions manually.

Use the **In / Out** filter at the top to switch between:
- **In** — stock arrivals (supplies, cancellation reversals)
- **Out** — stock departures (orders)

---

### 👥 Managing Employees (Admin Only)

1. Go to **Employees** → **New Employee**
2. Fill in name, email, phone, and password
3. The role is automatically set to **Employee**
4. Employees can log in and use the system but cannot access the Users resource

> Employees are not visible to other employees — only the Admin can see and manage the user list.

---

## 🔐 Roles

| Feature | Admin | Employee |
|---|---|---|
| Dashboard | ✅ | ✅ |
| Products | ✅ | ✅ |
| Warehouses | ✅ | ✅ |
| Suppliers | ✅ | ✅ |
| Customers | ✅ | ✅ |
| Orders | ✅ | ✅ |
| Supplies | ✅ | ✅ |
| Stock Transactions | ✅ | ✅ |
| Employees (Users) | ✅ | ❌ |

---

## 🗄️ Database GUI

Adminer is available at **[http://localhost:8080](http://localhost:8080)** for browsing your database visually.

| Field | Value |
|---|---|
| System | MySQL |
| Server | `mysql` |
| Username | from your `.env` (`DB_USERNAME`) |
| Password | from your `.env` (`DB_PASSWORD`) |
| Database | from your `.env` (`DB_DATABASE`) |

---

## 📁 Project Structure

```
app/
├── Enums/              # OrderStatus, StockTransactionType, UserRole, ProductUnit
├── Filament/
│   └── Admin/
│       └── Resources/ # All Filament resources (Orders, Products, etc.)
├── Models/             # Eloquent models
├── Observers/          # Business logic hooks (stock movements, status history)
database/
├── migrations/         # Database schema
├── seeders/            # Admin account seeder
```

---

## 📄 License

This project is open-sourced under the [MIT License](LICENSE).

---

<div align="center">

Built with ❤️ using Laravel & Filament

</div>