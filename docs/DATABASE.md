# Database Schema Overview

## 1. Primary Tables
- **users**: Core user data.
- **submissions**: General submissions (LAPKIN, Survailen, Verifikasi).
- **sinarx_submissions**: Specific data for X-Ray amendments.
- **ktun_deliveries**: Tracking of document deliveries.
- **activity_logs**: Audit trail for admin actions.

## 2. Relationships
- User -> Submissions: One-to-Many.
- Submission -> SubmissionFiles: One-to-Many.
