-- Stride Log database. Import this file in phpMyAdmin to create the database,
-- the tables and a demo account. Tables are dropped first, so importing again resets all data.
CREATE DATABASE IF NOT EXISTS fitness_tracker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fitness_tracker;

DROP TABLE IF EXISTS workouts;
DROP TABLE IF EXISTS messages;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  username VARCHAR(30) NOT NULL,
  email VARCHAR(120) NOT NULL,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_username (username),
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Each workout belongs to a user; ON DELETE CASCADE removes a user's workouts with the account.
CREATE TABLE workouts (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT UNSIGNED NOT NULL,
  activity VARCHAR(50) NOT NULL,
  duration INT UNSIGNED NOT NULL,
  calories_burned INT UNSIGNED NOT NULL DEFAULT 0,
  steps INT UNSIGNED NOT NULL DEFAULT 0,
  workout_date DATE NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_workouts_user_date (user_id, workout_date),
  CONSTRAINT fk_workouts_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE messages (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(80) NOT NULL,
  email VARCHAR(120) NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Demo account: username demo, password Demo@1234 (stored as a hash).
INSERT INTO users (username, email, password) VALUES
('demo', 'demo@fitnesstracker.lk', '$2y$10$eKb..s5c8DgMblkh7xOS2ekf1MxNGK94L86Gwjj7td1vrX7EApjNW');

-- Sample workouts for the last 7 days so the chart has data on first login.
INSERT INTO workouts (user_id, activity, duration, calories_burned, steps, workout_date) VALUES
(1, 'Running', 35, 340, 5200, CURDATE() - INTERVAL 6 DAY),
(1, 'Strength training', 50, 280, 1800, CURDATE() - INTERVAL 5 DAY),
(1, 'Walking', 45, 190, 6100, CURDATE() - INTERVAL 4 DAY),
(1, 'Cycling', 60, 460, 2400, CURDATE() - INTERVAL 3 DAY),
(1, 'Yoga', 30, 120, 900, CURDATE() - INTERVAL 2 DAY),
(1, 'Running', 40, 395, 5900, CURDATE() - INTERVAL 1 DAY),
(1, 'HIIT', 25, 300, 2100, CURDATE());
