# Product Requirements Document (PRD) - SI-MUTU Pro

## 1. Project Overview
**SI-MUTU Pro** (Layanan KFJM) is a centralized web application for managing quality assurance, regulatory compliance, and licensing services. It serves as a bridge between various testing/training agencies and the central regulatory body (BAPETEN).

## 2. Target Audience
- **Internal Admin:** Personnel responsible for verifying agency registrations and submissions.
- **Training Agencies (Pelatihan):** Entities providing training services.
- **Testing Agencies (Lembaga Uji):** Entities performing testing and calibration.
- **X-Ray Applicants (Sinar-X):** Entities applying for certificate amendments related to X-ray equipment.

## 3. Key Features
### 3.1. Multi-Portal Authentication
- Custom registration based on agency category (Pelatihan, Uji, Sinar-X).
- Multi-portal login that redirects users to their specific dashboard based on their category and role.
- Admin approval system for new user registrations.

### 3.2. Document Management & Submissions
- **LAPKIN (Laporan Kinerja):** Performance report submission for Pelatihan.
- **Survailen (Surveillance):** Periodic evaluation flow involving document uploads and self-assessments.
- **Verifikasi (Verification):** Verification of appointments or credentials.
- **Sinar-X Amandemen:** Specialized workflow for amending X-ray certificates.

### 3.3. Reporting & Exports
- Export user data and logs to Excel (via Maatwebsite Excel).
- Generate certificates and reports in PDF (via Laravel DomPDF).
- Activity logging for audit trails.

### 3.4. Surat Kuasa (Power of Attorney) - Planned
- Template download available on all registration pages.
- Requirement for users to upload a signed Surat Kuasa during registration.
- Admin review and validation of Surat Kuasa before account approval.

## 4. Non-Functional Requirements
- **Security:** Role-based access control (RBAC).
- **Usability:** Responsive design using Tailwind CSS.
- **Scalability:** Built on Laravel 10 for maintainable growth.
