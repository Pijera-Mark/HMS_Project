# HMS Admin Policy

## Single Administrator System

This Hospital Management System (HMS) is configured to operate with a **single administrator account** only.

## Admin Credentials

**Email:** `admin@hospital.com`  
**Password:** `Hospital@2024`  
**Role:** `admin`  
**Status:** `active`

## System Restrictions

### Admin Account Creation
- ❌ **Cannot create** additional admin accounts
- ❌ **Admin role** is hidden in user creation when admin exists
- ✅ **Only one admin** account is permitted in the system

### Admin Account Protection
- ❌ **Cannot delete** the admin account
- ❌ **Cannot deactivate** the admin account
- ❌ **Cannot change** the admin role to another role
- ✅ **Can edit** admin details (name, email, password)
- ✅ **Can reset** admin password securely

### User Management
- ✅ **Can create** users with other roles (doctor, nurse, etc.)
- ✅ **Can edit** non-admin users
- ✅ **Can deactivate** non-admin users
- ✅ **Can delete** non-admin users

## Security Features

### Password Requirements
- Minimum 8 characters
- Must contain uppercase letters
- Must contain lowercase letters  
- Must contain numbers
- Must contain special characters

### Session Management
- Secure session handling
- Automatic logout on inactivity
- Password reset tracking

### Activity Logging
- All admin actions are logged
- User management activities tracked
- Security events monitored

## Emergency Procedures

### If Admin Account is Compromised
1. **Immediately change** the admin password
2. **Review activity logs** for suspicious actions
3. **Deactivate affected user accounts**
4. **Notify security team**

### Password Reset
1. **Use secure password reset** functionality
2. **Generate strong temporary password**
3. **Communicate new password securely**
4. **Require password change on next login**

## Best Practices

### Admin Security
- **Use strong, unique passwords**
- **Change passwords regularly**
- **Enable two-factor authentication** (when available)
- **Monitor login activities**

### User Management
- **Review user permissions regularly**
- **Remove inactive accounts**
- **Follow principle of least privilege**
- **Document user access changes**

## Compliance

### Healthcare Standards
- **HIPAA compliance** considerations
- **Audit trail requirements**
- **Data protection policies**
- **Access control standards**

### System Governance
- **Single point of control** for system administration
- **Clear accountability** for administrative actions
- **Documented procedures** for admin tasks
- **Regular security audits**

## Contact Information

For admin-related issues:
- **System Administrator:** admin@hospital.com
- **Emergency Contact:** [Hospital IT Department]
- **Documentation:** See HMS User Management Guide

## Important Notes

- This system is designed for **single admin operation**
- Any attempt to create multiple admin accounts will be **blocked**
- Admin account is **protected** from accidental deletion
- All admin actions are **logged for audit purposes**
- Regular **security reviews** are recommended
