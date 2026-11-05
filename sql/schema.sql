-- Junk Food Awareness Management System — database schema + sample data
-- Import in phpMyAdmin (http://localhost/phpmyadmin) or: mysql -u root < sql/schema.sql

CREATE DATABASE IF NOT EXISTS jfams CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE jfams;

DROP TABLE IF EXISTS reports;
DROP TABLE IF EXISTS activities;
DROP TABLE IF EXISTS programs;
DROP TABLE IF EXISTS profile;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  user_id   INT AUTO_INCREMENT PRIMARY KEY,
  username  VARCHAR(50)  NOT NULL UNIQUE,
  password  VARCHAR(255) NOT NULL,              -- bcrypt hash, never plain text
  role      ENUM('admin','user') NOT NULL DEFAULT 'user',
  email     VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE profile (
  profile_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id    INT NOT NULL UNIQUE,
  name       VARCHAR(100) NOT NULL,
  roll_no    VARCHAR(30),
  branch     VARCHAR(60),
  college    VARCHAR(120),
  phone      VARCHAR(20),
  address    VARCHAR(200),
  batch      VARCHAR(80),
  photo      VARCHAR(200),
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE programs (
  program_id  INT AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(120) NOT NULL,
  tagline     VARCHAR(160),
  description TEXT,
  date        DATE NOT NULL,
  location    VARCHAR(120) NOT NULL,
  icon        VARCHAR(8) DEFAULT '🥗'
) ENGINE=InnoDB;

CREATE TABLE activities (
  activity_id   INT AUTO_INCREMENT PRIMARY KEY,
  program_id    INT NULL,
  activity_name VARCHAR(120) NOT NULL,
  activity_date DATE NOT NULL,
  location      VARCHAR(120) NOT NULL,
  status        ENUM('Completed','Pending') NOT NULL DEFAULT 'Pending',
  FOREIGN KEY (program_id) REFERENCES programs(program_id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE reports (
  report_id     INT AUTO_INCREMENT PRIMARY KEY,
  program_id    INT NOT NULL,
  participants  INT NOT NULL DEFAULT 0,
  volunteers    INT NOT NULL DEFAULT 0,
  male_count    INT NOT NULL DEFAULT 0,
  female_count  INT NOT NULL DEFAULT 0,
  feedback      DECIMAL(2,1) NOT NULL DEFAULT 0,   -- rating out of 5
  before_score  TINYINT NOT NULL DEFAULT 0,        -- % aware before program
  after_score   TINYINT NOT NULL DEFAULT 0,        -- % aware after program
  report_date   DATE NOT NULL,
  FOREIGN KEY (program_id) REFERENCES programs(program_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Sample data (users + profiles are created by install.php so passwords are hashed correctly)
INSERT INTO programs (title, tagline, description, date, location, icon) VALUES
('Healthy Eating Seminar','Importance of a Balanced Diet','A seminar explaining macronutrients, portion control and building a balanced daily plate.','2026-07-02','College Auditorium','🥗'),
('Poster Presentation','Harmful Effects of Junk Food','Student-designed posters highlighting obesity, diabetes and heart disease risks.','2026-07-05','Campus Grounds','🖼️'),
('Quiz Competition','Test your food awareness','Interactive quiz competition testing nutrition knowledge, with prizes for top scorers.','2026-07-08','Seminar Hall','📝'),
('Healthy Cooking Demonstration','Cook it fresh, cook it healthy','Live demo of quick, healthy recipes as alternatives to fast food and packaged snacks.','2026-07-12','Community Hall','👩‍🍳'),
('Nutrition Awareness Campaign','Village outreach drive','Door-to-door and public campaign spreading nutrition awareness in nearby villages.','2026-07-15','Nearby Village','📢');

INSERT INTO activities (program_id, activity_name, activity_date, location, status) VALUES
(2,'Poster Campaign','2026-07-10','Government School','Completed'),
(1,'Health Seminar','2026-07-12','Community Hall','Completed'),
(5,'Healthy Food Rally','2026-07-15','Village','Pending');

INSERT INTO reports (program_id, participants, volunteers, male_count, female_count, feedback, before_score, after_score, report_date) VALUES
(1,180,8,100,80,4.5,40,75,'2026-07-03'),
(2,220,10,120,100,4.6,35,80,'2026-07-06'),
(3,150,6,80,70,4.7,38,78,'2026-07-09'),
(4,110,6,55,55,4.8,42,85,'2026-07-13'),
(5,620,12,345,275,4.4,30,72,'2026-07-16');
