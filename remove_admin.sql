-- Remove admin@hms.com account from database
-- This script will remove the old admin account

-- Delete the old admin account
DELETE FROM users WHERE email = 'admin@hms.com';

-- Verify removal
SELECT COUNT(*) as admin_count FROM users WHERE email = 'admin@hms.com';

-- Show current admin accounts
SELECT id, name, email, role, status, created_at FROM users WHERE role = 'admin';
