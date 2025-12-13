# Hospital Management System (HMS)

A comprehensive Hospital Management System built with CodeIgniter 4 framework.

## Features

- **Patient Management**: Complete CRUD operations for patient records, medical history, and allergies
- **Doctor Management**: Manage doctor profiles, specializations, schedules, and availability
- **Appointment System**: Schedule, confirm, cancel, and manage patient appointments
- **Medicine Inventory**: Track medicines, stock levels, expiry dates, and dispensing
- **Admission Management**: Handle patient admissions, ward assignments, and discharges
- **Billing & Invoicing**: Generate invoices, track payments, and manage billing
- **RESTful API**: Complete REST API for all operations

## Tech Stack

- **Backend**: CodeIgniter 4 (PHP 8.1+)
- **Database**: MySQL/MariaDB
- **API**: RESTful JSON API

## Important Change with index.php

`index.php` is no longer in the root of the project! It has been moved inside the *public* folder,
for better security and separation of components.

This means that you should configure your web server to "point" to your project's *public* folder, and
not to the project root. A better practice would be to configure a virtual host to point there. A poor practice would be to point your web server to the project root and expect to enter *public/...*, as the rest of your logic and the
framework are exposed.

**Please** read the user guide for a better explanation of how CI4 works!

## Repository Management

We use GitHub issues, in our main repository, to track **BUGS** and to track approved **DEVELOPMENT** work packages.
We use our [forum](http://forum.codeigniter.com) to provide SUPPORT and to discuss
FEATURE REQUESTS.

This repository is a "distribution" one, built by our release preparation script.
Problems with it can be raised on our forum, or as issues in the main repository.

## Contributing

We welcome contributions from the community.

Please read the [*Contributing to CodeIgniter*](https://github.com/codeigniter4/CodeIgniter4/blob/develop/CONTRIBUTING.md) section in the development repository.

## Server Requirements

PHP version 8.1 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

> [!WARNING]
> - The end of life date for PHP 7.4 was November 28, 2022.
> - The end of life date for PHP 8.0 was November 26, 2023.
> - If you are still using PHP 7.4 or 8.0, you should upgrade immediately.
> - The end of life date for PHP 8.1 will be December 31, 2025.

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mysqli](http://php.net/manual/en/mysqli.installation.php) if you plan to use MySQL
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library

## Project Structure

The HMS project follows a modular structure with enhanced security and performance features:

- `/app/Controllers/` - All application controllers
- `/app/Models/` - Database models and business logic
- `/app/Views/` - View templates and frontend components
- `/app/Libraries/` - Custom libraries including Security and JWT libraries
- `/app/Config/` - Configuration files
- `/public/` - Public assets and entry point
- `/writable/` - Writable directory for logs and cache

## Security Features

This HMS implementation includes comprehensive security measures:

- Advanced authentication with JWT tokens
- CSRF protection for all forms
- Security audit logging
- Input sanitization and validation
- Rate limiting and IP blocking
- Encrypted sensitive data storage
- Session management with timeout
- Multi-layer security filters

## Performance Optimizations

- Multi-level caching system (memory and file)
- Database query optimization
- Performance monitoring and analytics
- Automatic cache warming
- Resource usage tracking
