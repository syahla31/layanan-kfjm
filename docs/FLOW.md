# System Flows - SI-MUTU Pro

## 1. Registration & Approval Flow
1. **User** visits specific registration page (e.g., /pelatihan/register).
2. **User** fills form and uploads **Surat Kuasa** (Mandatory).
3. **Account** is created with status pending.
4. **Internal Admin** logs in and sees pending accounts in the dashboard.
5. **Internal Admin** reviews the **Surat Kuasa** and agency details.
6. **Internal Admin** either **Approves** (status becomes active) or **Rejects** (account deleted).
7. **User** can only log in once status is active.

## 2. Login Redirect Flow
1. **User** enters credentials at common or specific login page.
2. **System** verifies credentials and active status.
3. **System** checks auth()->user()->category.
4. **User** is redirected to /{category}/dashboard.
