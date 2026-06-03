# GEMINI.md - SI-MUTU Pro Project Context

## Project Overview
**SI-MUTU Pro** (Layanan KFJM) is a robust web application built with Laravel 10 designed to manage quality assurance and regulatory services. The system is divided into four main functional areas:
1.  **Internal (Admin Pusat):** Centralized administration and user management.
2.  **Pelatihan (Training):** Management of training documents, performance reports (LAPKIN), surveillance, and verification.
3.  **Lembaga Uji (Testing Agency):** Management of testing agency reports, surveillance, and verification.
4.  **Sinar-X (X-Ray):** Management of X-ray related submissions and amendments.

The application uses a multi-portal login system, directing users to specific dashboards based on their category (Internal, Pelatihan, Uji, or Sinarx).

## Core Technology Stack
-   **Framework:** Laravel 10.x (PHP 8.1+)
-   **Frontend:** Blade Templates, Tailwind CSS, Vite
-   **Authentication:** Laravel Breeze (Customized for multi-portal support)
-   **Key Libraries:**
    -   `barryvdh/laravel-dompdf`: PDF generation (e.g., certificates, reports).
    -   `maatwebsite/excel`: Excel import/export functionality.
    -   `laravel/sanctum`: API authentication (if applicable).
    -   `spatie/laravel-ignition`: Error handling.

## Building and Running
To set up the project locally, follow these standard Laravel procedures:

### Prerequisites
-   PHP 8.1 or higher
-   Composer
-   Node.js & NPM
-   MySQL or similar database

### Installation Steps
1.  **Clone the repository.**
2.  **Install PHP dependencies:**
    ```bash
    composer install
    ```
3.  **Install Frontend dependencies:**
    ```bash
    npm install
    ```
4.  **Environment Setup:**
    -   Copy `.env.example` to `.env`.
    -   Configure database settings in `.env`.
    -   Generate application key:
        ```bash
        php artisan key:generate
        ```
5.  **Database Migration & Seeding:**
    ```bash
    php artisan migrate --seed
    ```
6.  **Run Development Server:**
    ```bash
    php artisan serve
    ```
7.  **Compile Assets:**
    ```bash
    npm run dev
    ```

## Project Structure & Conventions
-   **Routing:** Defined in `routes/web.php`, organized by module prefix (`/internal`, `/pelatihan`, `/uji`, `/sinarx`).
-   **Controllers:** Located in `app/Http/Controllers/`. Specific logic for modules is often handled by `SurvailenController`, `VerifikasiController`, and `SubmissionController`.
-   **Models:** Located in `app/Models/`. Key models include `User`, `Submission`, `SinarxSubmission`, `KtunDelivery`, and `ActivityLog`.
-   **Views:** Located in `resources/views/`, categorized by module:
    -   `resources/views/internal/`
    -   `resources/views/pelatihan/`
    -   `resources/views/uji/`
    -   `resources/views/sinarx/`
-   **Migrations:** Database schema is managed via `database/migrations/`.
-   **Custom Auth:** The project uses `CustomLoginController` and `CustomRegisterController` to handle category-specific registration and login flows.

## Engineering Standards & Best Practices
To maintain high-quality, maintainable, and scalable code, all contributions must adhere to the following standards:

### 1. Clean Code & Readability
- **Meaningful Names:** Use clear, descriptive names for variables, functions, and classes (e.g., `calculateMonthlyRevenue()` instead of `calcRev()`).
- **Small Functions:** Keep functions focused on a single task. Ideally, a function should be short and easy to reason about.
- **Self-Documenting Code:** Prioritize clear logic over excessive commenting. Use comments only to explain "why" something complex is done, not "what" is happening.

### 2. SOLID Principles
- **Single Responsibility (SRP):** Each class/controller should have one reason to change. Use Services or Actions for complex business logic instead of bloating Controllers.
- **Open/Closed (OCP):** Software entities should be open for extension but closed for modification. Use interfaces and polymorphism where appropriate.
- **Liskov Substitution (LSP):** Subclasses should be replaceable by their base classes without affecting correctness.
- **Interface Segregation (ISP):** Avoid forcing classes to implement methods they don't use. Split large interfaces into smaller, specific ones.
- **Dependency Inversion (DIP):** Depend on abstractions (interfaces), not concretions. Use Laravel's Service Container for dependency injection.

### 3. DRY (Don't Repeat Yourself)
- **Abstract Common Logic:** Move repetitive logic into Traits, Base Classes, or dedicated Helper/Service classes.
- **Reusable Blade Components:** For the frontend, extract repeating UI patterns into Blade Components (e.g., `x-primary-button`, `x-modal`).

### 4. Code Reusability & Composition
- **Composition over Inheritance:** Prefer building complex objects by combining simpler ones rather than using deep inheritance hierarchies.
- **Generic Helpers:** Design utility functions to be as generic as possible to ensure they can be used across different modules (e.g., a generic `FileUploader` service).

### 5. Laravel Specific Standards
- **Eloquent Best Practices:** Avoid N+1 queries by using eager loading (`with()`). Use Scopes for common query filters.
- **Request Validation:** Always use Form Request classes for validation to keep Controllers clean.
- **Service Layer:** For complex business flows (like the Survailen evaluation process), use a dedicated Service class.

## Development Guidelines
-   **Role-Based Access:** Always check `auth()->user()->role` and `auth()->user()->category` when implementing new features.
-   **Styling:** Follow the existing Tailwind CSS patterns. Custom CSS is located in `public/css/custom.css` and `resources/css/app.css`.
-   **File Storage:** Document uploads are common (e.g., Survailen documents); ensure proper handling via Laravel's `Storage` facade.
-   **Exports:** Use the integrated Excel and PDF libraries for any report-related tasks.
