> **Note:** This document should be updated at the beginning of each session to track the project's evolution and maintain a shared understanding.

# Project: Reading Platform

## In-Depth Analysis

### Database Schema

The database schema is defined by a series of Laravel migration files. The key tables are:

-   **`users`**: A central table for all user types, distinguished by a `role` enum (`admin`, `author`, `school`, `student`, `reader`, `adult_reader`). It includes personal information, authentication details, and foreign keys for relationships like `school_id` and `parent_id`.
-   **`books`**: Contains all information about the books, including title, description, author, category, files (PDF/audio), pricing, and status. It also has a pivot table `book_tag` for a many-to-many relationship with tags.
-   **`schools`**: Manages educational institutions, with details like name, address, access codes for students, subscription information, and customization options (logo, colors).
-   **`subscriptions`** and **`subscription_plans`**: Handle the subscription-based access model for both individual readers and schools.
-   **`classes`** and **`class_student`**: Manage virtual classes within schools, linking students to specific classes.
-   **`quizzes`**, **`questions`**, and **`quiz_attempts`**: A complete system for creating and managing quizzes associated with books, and for tracking student attempts and scores.
-   **`purchases`**: Tracks individual book purchases (PDF or audio).
-   **`reading_progress`** and **`audio_progress`**: Monitor user progress in books and audiobooks.
-   **`conversations`** and **`messages`**: Power the real-time messaging system between users.
-   **`revenues`** and **`author_payouts`**: Manage the financial aspects, tracking revenue from book sales and calculating payouts for authors.

### Routing and Controllers

The application uses a modular, role-based routing system, which is a key architectural feature.

-   **`routes/web.php`**: Acts as the main entry point. It defines public routes (homepage, login, book details) and then loads role-specific route files based on user authentication and middleware.
-   **Role-Based Route Files** (`routes/admin.php`, `routes/student.php`, etc.): Each file contains routes for a specific user role, prefixed accordingly (e.g., `/student/dashboard`). This keeps the concerns for each role neatly separated.
-   **Controllers**: Controllers are organized by role in the `app/Http/Controllers` directory (e.g., `app/Http/Controllers/Student`). They follow the standard Laravel pattern, handling incoming requests, interacting with models, and returning views. For example, `Student\DashboardController` gathers all necessary data for the student's main dashboard.

### User Model (`app/Models/User.php`)

The `User` model is the core of the application's business logic.

-   **Roles**: It uses a single `role` column to manage different user types, with specific methods like `isAdmin()`, `isStudent()`, etc., for easy checking.
-   **Relationships**: It defines numerous Eloquent relationships that map directly to the database schema, such as `books()` for authors, `school()` for students, `subscriptions()`, `classes()`, and `conversations()`.
-   **Access Control**: The `hasAccessToBook(Book $book)` method is a critical piece of business logic. It centralizes the complex rules for determining if a user can access a specific book, checking for admin rights, authorship, school subscriptions, individual purchases, or class assignments.

### Frontend

The frontend is built with a traditional server-side rendering approach, enhanced with JavaScript.

-   **`vite.config.js`**: Configured to use Laravel Vite Plugin, which compiles `resources/css/app.css` (which imports Tailwind CSS) and `resources/js/app.js` into the final assets.
-   **`package.json`**:
    -   **Dependencies**: `bootstrap` is listed as a dependency.
    -   **Dev Dependencies**: The primary tools are `vite`, `laravel-vite-plugin`, `tailwindcss`, and `axios`. `axios` is used for making asynchronous requests from the frontend to the backend API endpoints.
-   **Stack Summary**: The stack is a combination of Bootstrap and Tailwind CSS for styling, with vanilla JavaScript and `axios` for interactivity, all compiled through Vite. There is no major JavaScript framework like React or Vue.js.

---

## Project Analysis Summary

The project is a large, feature-rich 'Reading Platform' built with Laravel 12 and a traditional server-side rendered frontend. The architecture is a well-organized 'Modular Monolith', with clear separation of concerns based on user roles (Admin, Author, Teacher, Student, etc.). This modularity is evident in the file structure of the routes and controllers.

**Backend:**
- **Framework:** Laravel 12 / PHP 8.2
- **Key Dependencies:** Maatwebsite/Excel (for data import/export), Intervention/Image (image handling), Sanctum (API auth), PDFParser, and QRCode generator.
- **Database:** Defaults to SQLite for local development, but is configured for MySQL/Postgres. It uses the database for queues, sessions, and caching by default, but Redis is also configured and likely intended for production use to improve performance.

**Frontend:**
- **Architecture:** Server-side rendered Blade templates with 'sprinkles' of JavaScript for interactivity.
- **Build Tool:** Vite.
- **Styling:** Tailwind CSS 4.0 is the primary framework.
- **JavaScript:** No major framework like Vue or React. `axios` is used for making AJAX calls to stateful API endpoints.

**Core Application Domains (revealed by the database schema):**
- **Content Management:** A library of books, categories, tags, and reviews.
- **E-commerce:** A subscription system, one-off purchases, and a financial system for author payouts.
- **Education:** Management of schools, classes, student assignments, and announcements.
- **Gamification:** Quizzes, progress tracking, badges, and leaderboards.
- **Social Features:** A complete messaging system between users.

The project is well-structured, modern, and built with standard, professional tools. The separation of concerns by user role makes the large codebase manageable.

## Key Files

- **`composer.json`**: Defines the backend framework (Laravel 12) and key PHP dependencies, indicating features like data export.
- **`package.json`**: Defines the frontend stack, showing the use of Vite for building, Tailwind for styling, and Axios for AJAX calls. It notably lacks a major JS framework.
- **`routes/`**: The file structure in this directory is the primary indicator of the 'Modular Monolith' architecture, with routes separated by user role.
- **`routes/web.php`**: This file acts as the central router, loading the role-based route files and applying the necessary middleware for authentication and authorization.
- **`database/migrations/`**: This directory provides the definitive schema for the entire application database, revealing the core data entities and the relationships between them. It's the blueprint of the application's features.

---

## Project Overview

This project is a comprehensive digital reading and listening platform built with Laravel (PHP) for the backend and a modern JavaScript/CSS frontend (Vite, Bootstrap, TailwindCSS). Its primary goal is to promote reading through three distinct "universes":

1.  **Public Space:** For general readers and enthusiasts of African literature.
2.  **Educational Space:** Designed for educational institutions, featuring virtual classes and integrated quizzes.
3.  **Adult Space:** For adult-oriented works, accessible via invitation only.

The platform supports multiple user roles, each with tailored dashboards and functionalities:
*   **Admin:** Full control over users, books, payments, subscriptions, and analytics.
*   **Author:** Manages published books, tracks statistics, and monitors earnings.
*   **School:** Manages classes, students, assigns educational books, and tracks student progress.
*   **Student:** Accesses educational content assigned by their school, takes quizzes, and tracks personal progress.
*   **Reader:** Accesses public content via subscription or individual purchases.
*   **Adult Reader:** Accesses restricted adult content via invitation.

Key features include:
*   Multi-role authentication and authorization.
*   Book management (PDF, Audio, cover images, metadata).
*   Reading and listening with progress tracking.
*   Quiz functionality (with a future goal for AI-powered generation).
*   Subscription management (individual and institutional).
*   Secure content delivery and download options with discounts for subscribers.
*   Messaging system.
*   Gamification elements (badges).
*   Multilingual support (primarily French and English, with African languages planned).
*   PWA (Progressive Web App) capabilities with planned offline mode.

## Architecture

The project follows a modular Laravel architecture, separating concerns into distinct modules for public, educational, adult, author, and admin spaces.

*   **Backend:** Laravel (PHP 8.2+)
    *   **Database:** MySQL (default, but configurable for SQLite, MariaDB, PostgreSQL, SQLSRV).
    *   **Authentication:** Laravel Sanctum for API authentication.
    *   **Image Handling:** Intervention Image.
    *   **PDF Parsing:** Smalot/PDFParser.
    *   **Excel/CSV:** Maatwebsite/Excel.
*   **Frontend:**
    *   **Build Tool:** Vite.
    *   **Frameworks/Libraries:** Bootstrap 5, TailwindCSS, JavaScript.

## Building and Running

### Prerequisites

*   PHP 8.2 or higher
*   Composer
*   Node.js and npm/yarn
*   A database (e.g., MySQL)

### Setup (First-time installation)

The `composer.json` includes a comprehensive `setup` script:

```bash
composer run setup
```

This script performs the following.
1.  Installs PHP dependencies via Composer.
2.  Copies `.env.example` to `.env` if `.env` doesn't exist.
3.  Generates the application key.
4.  Runs database migrations.
5.  Installs Node.js dependencies via npm.
6.  Builds frontend assets using Vite.

**Manual Setup Steps (if `composer run setup` fails or for specific needs):**

1.  **Install PHP Dependencies:**
    ```bash
    composer install
    ```
2.  **Configure Environment:**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    Edit `.env` to configure your database connection and other settings.
3.  **Run Database Migrations:**
    ```bash
    php artisan migrate
    ```
    (Optional: `php artisan db:seed` to populate with seed data if available).
4.  **Install Node.js Dependencies:**
    ```bash
    npm install
    # or yarn install
    ```
5.  **Build Frontend Assets:**
    ```bash
    npm run build
    # or yarn build
    ```
    > **Agent Note:** Do NOT run `npm run build` or `npm run dev` unless explicitly instructed by the user, as this can interfere with the local development environment or cause unintended side effects.

### Development

To run the application in development mode, use the `dev` script defined in `composer.json`:

```bash
composer run dev
```

This command concurrently starts:
*   `php artisan serve`: Laravel development server.
*   `php artisan queue:listen --tries=1`: Listens for queue jobs.
*   `php artisan pail --timeout=0`: Monitors logs.
*   `npm run dev`: Vite development server with HMR for frontend assets.

### Testing

To run the project's tests:

```bash
composer run test
```

This clears the configuration cache and runs `php artisan test`.

## Development Conventions

*   **Coding Style:** Laravel's conventions are generally followed. `laravel/pint` is used for code style fixing.
*   **Frontend:** Uses Bootstrap 5 and TailwindCSS for styling, managed by Vite.
*   **Database:** Laravel Eloquent ORM and migrations are used for database interactions and schema management.
*   **Roles & Permissions:** A robust role-based access control system is implemented, with specific methods in the `User` model for role checking and access control (`hasAccessToBook`).
*   **Multilingual:** The application supports multiple languages, with French (`fr`) as the default locale.

## Remaining Tasks and Future Enhancements

Refer to the `todo.txt` file in the project root for a detailed and prioritized list of outstanding tasks and potential improvements. Key areas include refining the book purchase/download logic, integrating real payment gateways, and implementing AI-powered quiz generation.
