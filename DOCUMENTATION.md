# BDU Transit System Documentation

**Version:** 1.0.0
**Date:** December 2025

---

## 1. Introduction

**BDU Transit** is a comprehensive campus mobility management system designed for Bahir Dar University. It provides an interactive platform for students, staff, and administrators to manage and view transportation services.

### Key Features

- **Public Portal:** Real-time bus schedules, live map tracking, and announcements.
- **Role-Based Access:** Secure login for Students, Staff, and Administrators.
- **Admin Dashboard:** Centralized control center for managing the fleet, routes, timetables, and alerts.
- **Interactive Map:** Visual route tracking using Leaflet.js.
- **Professional Design:** Fully responsive, "glassmorphism" aesthetic built with vanilla CSS.

---

## 2. Installation & Setup

### Prerequisites

- **Web Server:** Apache (e.g., XAMPP, WAMP, LAMP stack).
- **PHP:** Version 7.4 or higher.
- **Database:** MySQL or MariaDB.

### Steps

1.  **Clone/Copy Files:** Place the project folder in your web server's root directory (e.g., `htdocs` or `/var/www/html`).
2.  **Database Setup:**
    - Create a new MySQL database named `bdu_transport`.
    - Import the `database.sql` file provided in the root directory.
    - _Default Admin Credentials:_ Username: `admin`, Password: `admin123`.
3.  **Configuration:**
    - Open `config/db_connect.php`.
    - Verify database credentials:
      ```php
      define('DB_HOST', 'localhost');
      define('DB_USER', 'root');
      define('DB_PASS', '');
      define('DB_NAME', 'bdu_transport');
      ```
4.  **Launch:**
    - Open your browser and navigate to `http://localhost/transport/`.

---

## 3. System Architecture

### Directory Structure

```
/transport
├── admin/                  # Admin-only pages
│   ├── dashboard.php       # Main Admin Control Center
│   ├── buses.php           # Fleet management
│   ├── routes.php          # Route creation
│   ├── schedules.php       # Timetable management
│   └── announcements.php   # Alert system
├── assets/
│   └── css/
│       └── style.css       # Core styling (Themes, Utilities)
├── config/
│   └── db_connect.php      # Database connection logic
├── includes/               # Reusable headers/footers
├── index.php               # Public Landing Page
├── login.php               # User Authentication
├── register.php            # User Registration
├── tracking.php            # Live Map Interface
└── schedules.php           # Public Schedules View
```

### Database Schema

- **users:** Stores credentials and roles (`admin`, `staff`, `student`).
- **buses:** Inventory of buses with status (`active`, `maintenance`).
- **routes:** Definition of transit paths (e.g., "Poly - City Center").
- **pickup_points:** Sequences of stops associated with routes (geo-coordinates).
- **schedules:** Linking buses to routes with time slots.
- **announcements:** Global messages displayed on the homepage.

---

## 4. User Manual

### public Users (Students/Staff)

1.  **Home Page:**
    - View active alerts scrolling at the top.
    - Quick access to "View Schedules" and "Live Tracking".
    - See system impact statistics.
2.  **Schedules:**
    - Navigate to `Schedules` via the navbar.
    - Use the **Search Bar** to filter trips by route name or bus number.
    - View departure times, bus numbers, and operating days.
3.  **Live Tracking:**
    - Navigate to `Live Tracking`.
    - Select a route from the "Trip Planner" sidebar.
    - Watch the simulated bus movement on the map.
    - View stop details along the route.

### Registration & Login

- **Sign Up:** Click "Join Now" to create an account. Choose "Student" or "Staff" role.
- **Login:** Access your account to personalize the experience (future features).

---

## 5. Administrator Manual

**Access:** Log in with an account having the `admin` role.

### Dashboard Modules

1.  **Overview:** View real-time counts of Buses, Routes, and Active Alerts.
2.  **Manage Buses:**
    - Add new buses with Driver Name and Plate Number.
    - Update status (e.g., mark a bus as "Maintenance" to remove it from active duty).
3.  **Manage Routes:**
    - Create new routes.
    - **Manage Stops:** Click on a route to add/edit GPS coordinates for stops.
4.  **Manage Schedules:**
    - Assign a Bus to a Route.
    - Set Departure Time and Operating Days.
5.  **Announcements:**
    - Post "Info", "Warning", or "Alert" messages.
    - Toggle visibility of messages appearing on the home page.

---

## 6. Security Features

- **Password Hashing:** All user passwords are securely hashed using PHP's `password_hash()`.
- **Session Control:** Admin pages are strictly protected; unauthorized access attempts redirect to login.
- **Input Sanitization:** Using PDO prepared statements to prevent SQL Injection.
- **XSS Protection:** Output escaping (`htmlspecialchars`) used on all user-generated content.

---

## 7. Troubleshooting

- **"Connection Failed":** Check `db_connect.php` credentials and ensure MySQL is running.
- **Map Not Loading:** Ensure you have an active internet connection (Leaflet.js requires external scripts).
- **Login Loop:** Clear browser cookies or check if `session_start()` is functioning on your server.
