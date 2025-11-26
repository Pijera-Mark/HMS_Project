# HMS Deployment Guide

## System Requirements
- PHP 8.0 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- SSL certificate for production
- Minimum 2GB RAM
- 10GB storage space

## Installation Steps

### 1. Database Setup
```sql
CREATE DATABASE hms_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'hms_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON hms_db.* TO 'hms_user'@'localhost';
FLUSH PRIVILEGES;
```

### 2. Configuration
- Copy `env.example` to `.env`
- Update database credentials
- Set base URL and encryption key
- Configure email settings
- Adjust session and cache settings

### 3. File Permissions
```bash
chmod 755 -R /path/to/hms
chmod 777 -R /path/to/hms/writable
chmod 777 -R /path/to/hms/public/uploads
```

### 4. Database Migration
```bash
php spark migrate
```

### 5. Initial Setup
- Access `/admin/setup` for initial configuration
- Create admin account
- Configure branches and departments
- Set up system preferences

## Security Configuration

### Apache (.htaccess)
```apache
# Prevent directory listing
Options -Indexes

# Hide .env files
<Files .env>
    Order allow,deny
    Deny from all
</Files>

# Security headers
Header always set X-Frame-Options DENY
Header always set X-Content-Type-Options nosniff
```

### PHP Settings
```ini
display_errors = Off
log_errors = On
error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT
session.cookie_httponly = 1
session.cookie_secure = 1
```

## Performance Optimization

### Caching
- Enable Redis/Memcached for production
- Configure cache settings in `.env`
- Set appropriate cache TTL values

### Database
- Enable query caching
- Optimize database indexes
- Regular database maintenance

### Web Server
- Enable gzip compression
- Set up CDN for static assets
- Configure browser caching

## Monitoring

### Logs
- Application logs: `writable/logs/`
- Error logs: `writable/logs/`
- Database logs: MySQL slow query log

### Health Checks
- Monitor disk space usage
- Check database connectivity
- Verify email functionality
- Test backup systems

## Backup Strategy

### Database Backups
```bash
# Daily backup
mysqldump -u hms_user -p hms_db > backup_$(date +%Y%m%d).sql

# Compress backups
gzip backup_*.sql
```

### File Backups
- Backup uploaded files regularly
- Store backups off-site
- Test backup restoration process

## Troubleshooting

### Common Issues
1. **Database Connection**: Check credentials and permissions
2. **File Permissions**: Verify writable directory permissions
3. **Session Issues**: Clear session cache
4. **Email Problems**: Check SMTP configuration

### Performance Issues
- Monitor slow queries
- Check cache hit rates
- Review server resources
- Optimize database queries

## Maintenance

### Regular Tasks
- Update software dependencies
- Review security logs
- Clean temporary files
- Update SSL certificates
- Test backup restoration

### Security Updates
- Apply PHP updates promptly
- Update third-party libraries
- Review security advisories
- Test updates in staging environment

## Support
- Check documentation for common issues
- Review error logs for troubleshooting
- Contact support for critical issues
- Keep system documentation updated
