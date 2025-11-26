-- Remove all admin accounts except admin@hospital.com
-- This script will clean up admin accounts and leave only admin@hospital.com

-- Remove all admin accounts except the desired one
DELETE FROM users WHERE role = 'admin' AND email != 'admin@hospital.com';

-- If admin@hospital.com doesn't exist, remove all admin accounts
-- (This will be handled by the seeder)

-- Verify removal
SELECT COUNT(*) as removed_admins FROM users WHERE role = 'admin' AND email != 'admin@hospital.com';

-- Show current admin accounts
SELECT id, name, email, role, status, created_at FROM users WHERE role = 'admin';

-- Expected result should only show admin@hospital.com
