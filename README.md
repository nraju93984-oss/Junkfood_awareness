# Junk Food Awareness Management System — PHP + MySQL backend

## Setup (XAMPP, ~3 minutes)
1. Install XAMPP, open the Control Panel, **Start Apache** and **Start MySQL**.
2. Copy this whole `jfams` folder into `C:\xampp\htdocs\` (Mac: `/Applications/XAMPP/htdocs/`).
3. Open http://localhost/phpmyadmin → **Import** → choose `sql/schema.sql` → Go.
   (Creates the `jfams` database, 5 tables and sample programs/activities/reports.)
4. Open **http://localhost/jfams/install.php** once. It creates the login accounts
   with hashed passwords. Then **delete install.php**.
5. Open **http://localhost/jfams/** and log in:
   - Admin: `admin` / `admin123` (can add / complete / delete activities)
   - Volunteer: `volunteer` / `volunteer123` (view-only + edit own profile)

Database settings (user `root`, empty password) are at the top of `api/config.php`.
Open the page via `http://localhost/...`, NOT by double-clicking the file.

## API endpoints (all JSON, session-cookie login)
| File | Method | Purpose | Access |
|---|---|---|---|
| api/login.php | POST | Log in | public |
| api/logout.php | any | Log out | any |
| api/session.php | GET | Current user (restores login on refresh) | logged in |
| api/dashboard.php | GET | Summary cards, chart data, recent activities | logged in |
| api/profile.php | GET / POST | Read / update own profile | logged in |
| api/activities.php | GET | List activities | logged in |
| api/activities.php | POST / PUT / DELETE | Add / change status / delete | admin |
| api/programs.php | GET / POST | List / add programs | GET: logged in, POST: admin |
| api/reports.php | GET | Totals + per-program report rows | logged in |

## Security built in
- Passwords stored with `password_hash` (bcrypt), checked with `password_verify`
- Every query uses PDO prepared statements (no SQL injection)
- Session ID regenerated on login; admin-only actions enforced on the server
- Output escaped in the browser (no script injection)

## Database (matches your design, plus a few extra columns the pages need)
- users(user_id, username, password, role, email)
- profile(profile_id, user_id, name, roll_no, branch, phone, address) + college, batch, photo
- programs(program_id, title, description, date, location) + tagline, icon
- activities(activity_id, program_id, activity_name, status) + activity_date, location
- reports(report_id, program_id, participants, volunteers, feedback, report_date) + male_count, female_count, before_score, after_score

## Troubleshooting
- "Cannot reach the server" → Apache isn't running, or you opened the file directly.
- "Database connection failed" → MySQL not running or schema.sql not imported.
- Blank/garbled JSON → turn on PHP errors in XAMPP and check `api/config.php` for typos.
