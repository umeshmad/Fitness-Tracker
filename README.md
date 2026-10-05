# Stride Log - Fitness Tracker

Stride Log is an interactive fitness tracker built with HTML, CSS, Bootstrap, JavaScript, PHP and MySQL. Users create an account, log workouts (activity, minutes, calories, steps, date), see their last seven days in an interactive chart, and filter their workout history by activity.

## Features

- Pages: Home (`index.php`), Features (`features.php`), Contact (`contact.php`), Dashboard (`dashboard.php`), Register, Login and Logout.
- Responsive layout built with Bootstrap 5 (local copy, no internet needed).
- User registration with `password_hash()` and login with sessions.
- Dashboard with weekly totals, an interactive chart (Chart.js) and a form to log workouts.
- Contact form that stores messages in the database.
- Server-side validation, prepared statements and CSRF tokens on every form.

### JavaScript features

| Feature | Where |
| --- | --- |
| Dynamic content updates (filtering, toggling) | Features page filter buttons and FAQ, dashboard activity filter and chart metric buttons |
| Interactive image slider (manual and automatic) | Home page, `js/slider.js` |
| Form validation | Every form, `js/validation.js` |
| Smooth scrolling | Navigation links that point to a section, `js/main.js` |
| Event handling (clicks, tooltips, pop-ups) | Feature detail modal, tooltips, delete confirmation |
| Custom animations | Hero entrance, reveal on scroll, animated week bars, slider fade |

## Requirements

- XAMPP or WAMP (PHP 7.4 or newer and MySQL or MariaDB)
- A web browser

## Setup

1. Copy this folder into your web root and keep the folder name `fitness_tracker`.
   - XAMPP: `C:\xampp\htdocs\fitness_tracker` (use your own XAMPP drive, for example `D:\xampp\htdocs\fitness_tracker`)
   - WAMP: `C:\wamp64\www\fitness_tracker`
2. Start **Apache** and **MySQL** from the XAMPP or WAMP control panel.
3. Open phpMyAdmin at `http://localhost/phpmyadmin`.
4. Click **Import**, choose `database.sql` from this folder, and click **Import** at the bottom. This creates the `fitness_tracker` database with the `users`, `workouts` and `messages` tables and a demo account.
5. Check `includes/db.php`. The defaults match a fresh XAMPP install (user `root`, empty password). Change them if your MySQL uses different credentials.
6. Open `http://localhost/fitness_tracker/` in your browser.

## Demo account

- Username: `demo`
- Password: `Demo@1234`

You can also create your own account from the Register page.

## Database

Database name: `fitness_tracker`

| Table | Columns |
| --- | --- |
| `users` | id, username, email, password (hashed), created_at |
| `workouts` | id, user_id, activity, duration, calories_burned, steps, workout_date, created_at |
| `messages` | id, name, email, message, created_at |

Deleting a user also deletes that user's workouts (`ON DELETE CASCADE`).

## Folder structure

```
fitness_tracker/
  css/            style.css, bootstrap.min.css
  js/             main.js, slider.js, validation.js, dashboard.js, chart.min.js, bootstrap.bundle.min.js
  images/         slide images and favicon (SVG)
  includes/       db.php, functions.php, header.php, footer.php
  auth/           register.php, login.php, logout.php
  contact.php
  features.php
  index.php
  dashboard.php
  README.md
  database.sql
```