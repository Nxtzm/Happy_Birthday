# QuickServe | Multi-Vendor Delivery Platform

QuickServe is a production-ready, local commerce platform designed with Clean Architecture and Material 3 principles. It supports multiple vendor types including Food, Grocery, Stationery, Meat, and Pharmacy.

## 🚀 Key Features

- **Four-in-One Application**: A single monolithic frontend that dynamically adapts based on user roles (Customer, Merchant, Rider, Admin).
- **Premium UI/UX**: Designed with Apple and Uber Eats quality in mind. Features include smooth transitions, soft shadows, and native Dark Mode support.
- **Clean Architecture**: Backend refactored into Domain, Infrastructure, and Application layers using the Repository Pattern.
- **Role-Based Access Control (RBAC)**: Strict authorization checks for all sensitive API endpoints.
- **Modular Payments**: Pluggable payment gateway system (COD and eSewa implemented).
- **Live Order Tracking**: Simulated real-time tracking with an animated map and rider marker.
- **Modular Inventory**: Merchant tools that adapt to business types (e.g., Prescription for Pharmacy, Expiry for Grocery).

## 🛠 Tech Stack

- **Backend**: PHP 8.3 (Monolithic)
- **Database**: SQLite 3
- **Frontend**: Vanilla JavaScript, CSS3 (Material 3), HTML5
- **Design**: Plus Jakarta Sans Typography, Material Symbols

## 📂 Architecture & Design

### Database Schema
- **Users**: Centralized auth with role-based attributes.
- **Stores & Products**: Relational mapping for multi-vendor inventory.
- **Orders & Items**: Atomicity handled via SQLite transactions.
- **Riders**: Wallet and status tracking.

### API Endpoints
- `?api=auth`: login, register, logout, me
- `?api=customer`: get_stores, get_store, search, place_order, get_orders
- `?api=merchant`: get_dashboard, update_status, get_products, save_product
- `?api=rider`: get_available, accept, complete
- `?api=admin`: get_stats, get_all_orders, get_merchants, get_riders

## 🔐 Permissions
- **Customer**: Browse, search, order, track.
- **Merchant**: Manage orders, inventory, and view earnings.
- **Rider**: Accept deliveries, verify with OTP, track wallet.
- **Admin**: Platform oversight, financial reports, and entity management.

## 📦 Getting Started
Run the built-in PHP server:
```bash
php -S localhost:8000 index.php
```
Default Admin Credentials:
- Phone: `02518899320`
- Password: `pass`
