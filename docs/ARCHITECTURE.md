# Architecture Overview - SI-MUTU Pro

## 1. Technology Stack
- **Framework:** Laravel 10.x
- **Language:** PHP 8.1+
- **Database:** MySQL
- **Frontend:** Blade Templates, Tailwind CSS, Vite
- **Auth:** Laravel Breeze (Heavily customized)
- **Reporting:** 
  - barryvdh/laravel-dompdf (PDF)
  - maatwebsite/excel (Excel)

## 2. Directory Structure (High Level)
- app/Http/Controllers/: Main logic handlers.
- app/Models/: Database representations (User, Submission, etc.).
- resources/views/: Blade templates organized by module (internal, pelatihan, uji, sinarx).
- routes/web.php: Main routing engine with role/category groups.
- database/migrations/: Schema evolution.
