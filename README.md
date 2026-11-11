## **Applicant:** Mark Angelu Eligan Pagsolingan
# Provincial Legislative Records (Ordinances and Resolutions)

Practical Examination - Computer Programmer I

A simple web application for managing and searching Provincial Ordinances and Provincial Resolutions.

- **Administrative area** (login required): add, view, edit, delete, search and filter records.
- **Public area** (no login): search and view-only access to ordinances and resolutions.

---

## 1. Technologies and Versions

| Technology | Version |
|---|---|
| PHP | 8.3 or higher |
| Laravel | 13.x (Laravel 12.x also works) |
| MySQL | 8.0 or higher (MariaDB 10.6+ also works) |
| Composer | 2.x |
| Frontend | Laravel Blade templates + plain CSS (`public/css/app.css`) |

No Node.js / NPM build step is needed. The CSS is a plain file.

## 2. Installation and Running

Requirements: PHP 8.3+ (with the `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json` extensions), Composer, and MySQL.

```bash
# 1. Install the PHP dependencies
composer install

# 2. Create your environment file and generate the app key
cp .env.example .env          # on Windows Command Prompt: copy .env.example .env
php artisan key:generate

# 3. Set your MySQL username/password in .env (see section 3), then create the tables and sample data
php artisan migrate --seed

# 4. Start the application
php artisan serve
```

Open **http://localhost:8000** in the browser.

| Page | URL |
|---|---|
| Public search | http://localhost:8000/ |
| Administrator login | http://localhost:8000/login |
| Administrative area | http://localhost:8000/admin |

## 3. Database Setup

1. Create an empty MySQL database:

   ```sql
   CREATE DATABASE provincial_legislation;
   ```

2. Open `.env` and set the database values (the defaults below match `.env.example`):

   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=provincial_legislation
   DB_USERNAME=your_mysql_username
   DB_PASSWORD=your_mysql_password
   ```

3. Run `php artisan migrate --seed`. This uses the Laravel migrations to create the tables and the seeder to add the administrator account and a few **fictional sample records**.

To start over with a clean database: `php artisan migrate:fresh --seed`

### Database tables

| Table | Main columns                                                                                                            |
|---|-------------------------------------------------------------------------------------------------------------------------|
| `users` | id, name, email (unique), password                                                                                      |
| `ordinances` | id, `ordinance_number` (unique), title, subject, date_passed (indexed), author_sponsor, summary (optional), timestamps  |
| `resolutions` | id, `resolution_number` (unique), title, subject, date_passed (indexed), author_sponsor, summary (optional), timestamps |

## 4. Default Login

| | |
|---|---|
| Email | `admin@example.com` |
| Password | `Admin@12345` |

The account is created by `php artisan migrate --seed`. User registration is not included (not required).
Please change this password for any real use.

## 5. Features Implemented

**Authentication**
- Login and logout for the pre-created administrator.
- All pages under `/admin` are protected. Guests who open them are redirected to the login page.
- Login attempts are rate-limited (10 per minute).

**Ordinances module (admin)** and **Resolutions module (admin)**
- Create, view list, view details, edit, and delete.
- A confirmation box appears before deleting.
- Search/filter by **number** (partial match), **title** (partial keyword match, e.g. `environ` finds "Environment"; several words must all appear), and **date passed** (exact date). Filters can be combined.
- Results are paginated (10 per page) and keep the search values when changing pages.
- Extra field: optional `summary`.

**Public legislative search (no login)**
- One search page for both Ordinances and Resolutions, with a selector (both / ordinances only / resolutions only).
- Exact and partial keyword search on the title (e.g. `agricultur` finds "Agriculture"). The keyword also matches the record number.
- Results are shown in separate sections with a colored **ORDINANCE** or **RESOLUTION** label.
- Each result opens a details page. Public users are view-only: there are no create/edit/delete buttons and `/admin` pages redirect to login.

**Validation (server-side)**
- Required: number, title, date passed, author/sponsor (blank or spaces-only values are rejected).
- Ordinance numbers and resolution numbers must be unique (the record's own number is ignored when editing).
- Date must be a real calendar date in `YYYY-MM-DD` format (for example `2026-02-30` is rejected) and may not be in the future.
- Error messages are shown at the top of the page and under each field. Entered values are kept after an error.

**Interface**
- The administrative area uses a dark red navigation bar labeled "ADMINISTRATIVE AREA". The public area uses a blue bar labeled "PUBLIC SEARCH".
- Navigation links: Dashboard, Ordinances, Resolutions, Public Site, Logout.

**Bulk Import**
- Administrators can import multiple ordinances and resolutions in a single operation using a bulk import file.
- Imported records are processed in bulk, reducing the need to encode each record individually.
- The system validates imported data, including required fields, document number uniqueness, and date validity.
- Invalid or duplicate records are identified and reported during the import process.
- Valid records are saved to the database, while invalid records are prevented from being imported.

## 6. Project Structure (main files)

```
app/Http/Controllers/
    AuthController.php          login / logout
    DashboardController.php     admin dashboard
    OrdinanceController.php     ordinances CRUD + search
    ResolutionController.php    resolutions CRUD + search
    PublicController.php        public search and details
app/Models/
    Ordinance.php, Resolution.php
    Concerns/SearchesLegislation.php    shared search code (partial keyword match)
app/Services/LegislationImporter.php    service for the bulk import
database/migrations/            ordinances and resolutions tables
database/seeders/DatabaseSeeder.php     admin account + sample records
resources/views/                Blade pages (layouts, auth, admin, ordinances, resolutions, public)
routes/web.php                  all routes
public/css/app.css              stylesheet
tests/Feature/                  automated tests
```

## 7. Automated Tests (optional)

```bash
php artisan test
```

The tests use an in-memory SQLite database (the `pdo_sqlite` PHP extension is needed) and do not touch the MySQL data.

## 8. Known Limitations

- There is a single administrator role. There is no user registration, password reset, or user management screen.
- Records have no file attachment (for example, a PDF copy of the ordinance); only text fields are stored.
- Date filter in the admin lists matches one exact date (no date range).
- Public search matches words inside the title (and the record number). It does not search the optional summary text and does not rank results by relevance; results are sorted by date passed, newest first.
- Sample records included by the seeder are fictional and for testing only.
