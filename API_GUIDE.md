# HMS API Documentation

## Overview

The Hospital Management System (HMS) provides a comprehensive RESTful API for managing hospital operations. This API supports multi-branch functionality with role-based access control.

## Base URL

```
http://localhost:8080/api/v1
```

## Authentication

All API endpoints (except login) require authentication. Include the session cookie in your requests.

## Response Format

All API responses follow this format:

### Success Response
```json
{
  "success": true,
  "message": "Operation completed successfully",
  "data": { ... }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error description",
  "error": "error_code",
  "errors": { ... }
}
```

## Common Error Codes

- `unauthorized` - Authentication required
- `insufficient_permissions` - User lacks required permissions
- `not_found` - Resource not found
- `validation_error` - Input validation failed
- `session_expired` - User session has expired

## Endpoints

### Authentication

#### POST /auth/login
Login user and create session.

**Request:**
```json
{
  "email": "user@example.com",
  "password": "userpassword"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "user@example.com",
      "role": "doctor",
      "branch_id": 1
    }
  }
}
```

#### POST /auth/logout
Logout user and destroy session.

**Response:**
```json
{
  "success": true,
  "message": "Logout successful"
}
```

### Patients

#### GET /patients
Get list of patients with filtering and pagination.

**Query Parameters:**
- `page` (int): Page number (default: 1)
- `limit` (int): Records per page (default: 20)
- `search` (string): Search term for name, email, phone
- `branch_id` (int): Filter by branch (admin only)

**Response:**
```json
{
  "success": true,
  "data": {
    "records": [
      {
        "patient_id": "PAT202400123",
        "first_name": "John",
        "last_name": "Doe",
        "email": "john.doe@example.com",
        "phone": "+1234567890",
        "date_of_birth": "1990-01-01",
        "gender": "Male",
        "blood_type": "O+",
        "created_at": "2024-01-01 10:00:00"
      }
    ],
    "pagination": {
      "page": 1,
      "limit": 20,
      "total": 150,
      "pages": 8
    }
  }
}
```

#### GET /patients/{id}
Get single patient by ID.

#### POST /patients
Create new patient.

**Request:**
```json
{
  "first_name": "Jane",
  "last_name": "Smith",
  "date_of_birth": "1985-05-15",
  "gender": "Female",
  "phone": "+1234567890",
  "email": "jane.smith@example.com",
  "emergency_contact": "Bob Smith",
  "emergency_phone": "+1234567891",
  "address": "123 Main St, City, State",
  "medical_history": "No significant medical history",
  "allergies": "Penicillin"
}
```

#### PUT /patients/{id}
Update patient information.

#### DELETE /patients/{id}
Delete patient (soft delete with dependency checks).

#### GET /patients/stats
Get patient statistics.

**Response:**
```json
{
  "success": true,
  "data": {
    "total": 150,
    "recent": 12,
    "growth_rate": 8.0
  }
}
```

### Doctors

#### GET /doctors
Get list of doctors.

#### GET /doctors/{id}
Get single doctor by ID.

#### POST /doctors
Create new doctor.

**Request:**
```json
{
  "name": "Dr. Sarah Johnson",
  "email": "sarah.johnson@hospital.com",
  "phone": "+1234567890",
  "specialization": "Cardiology",
  "license_number": "MD123456",
  "qualification": "MD, FACC",
  "experience_years": 10,
  "consultation_fee": 150.00,
  "available_days": "Monday,Tuesday,Wednesday,Thursday,Friday",
  "available_time_start": "09:00",
  "available_time_end": "17:00"
}
```

#### PUT /doctors/{id}
Update doctor information.

#### DELETE /doctors/{id}
Delete doctor.

### Appointments

#### GET /appointments
Get list of appointments.

**Query Parameters:**
- `date` (string): Filter by date (YYYY-MM-DD)
- `status` (string): Filter by status (scheduled, completed, cancelled)
- `doctor_id` (int): Filter by doctor
- `patient_id` (int): Filter by patient

#### GET /appointments/{id}
Get single appointment.

#### POST /appointments
Create new appointment.

**Request:**
```json
{
  "patient_id": 1,
  "doctor_id": 1,
  "scheduled_at": "2024-01-15 10:30:00",
  "purpose": "General checkup",
  "notes": "Patient complains of headache",
  "status": "scheduled"
}
```

#### PUT /appointments/{id}
Update appointment.

#### DELETE /appointments/{id}
Cancel/delete appointment.

### Medicines

#### GET /medicines
Get list of medicines.

**Query Parameters:**
- `search` (string): Search by name or category
- `category` (string): Filter by category
- `low_stock` (boolean): Get low stock items only

#### GET /medicines/{id}
Get single medicine.

#### POST /medicines
Add new medicine.

**Request:**
```json
{
  "name": "Paracetamol 500mg",
  "category": "Analgesic",
  "description": "Pain reliever and fever reducer",
  "manufacturer": "Pharma Corp",
  "unit_price": 5.50,
  "stock_quantity": 1000,
  "reorder_level": 100,
  "expiry_date": "2025-12-31"
}
```

#### PUT /medicines/{id}
Update medicine information.

#### DELETE /medicines/{id}
Delete medicine.

#### GET /medicines/low-stock
Get medicines with low stock.

#### GET /medicines/expiring
Get medicines expiring soon.

### Invoices

#### GET /invoices
Get list of invoices.

**Query Parameters:**
- `status` (string): Filter by status (paid, unpaid, partially_paid)
- `patient_id` (int): Filter by patient
- `date_from` (string): Filter by date range start
- `date_to` (string): Filter by date range end

#### GET /invoices/{id}
Get single invoice.

#### POST /invoices
Create new invoice.

**Request:**
```json
{
  "patient_id": 1,
  "items": [
    {
      "description": "Consultation Fee",
      "quantity": 1,
      "unit_price": 150.00,
      "amount": 150.00
    },
    {
      "description": "Blood Test",
      "quantity": 1,
      "unit_price": 50.00,
      "amount": 50.00
    }
  ],
  "total_amount": 200.00,
  "status": "unpaid"
}
```

#### PUT /invoices/{id}
Update invoice.

#### DELETE /invoices/{id}
Delete invoice.

### Dashboard

#### GET /dashboard/stats
Get dashboard statistics.

**Response:**
```json
{
  "success": true,
  "data": {
    "total_patients": 150,
    "total_doctors": 25,
    "total_appointments": 1200,
    "today_appointments": 15,
    "active_admissions": 8,
    "low_stock_medicines": 5,
    "expiring_medicines": 3,
    "unpaid_invoices": 12,
    "total_revenue": 50000.00,
    "pending_amount": 2500.00
  }
}
```

#### GET /dashboard/recent-appointments
Get recent appointments.

## Role-Based Permissions

### Admin
- Full access to all resources
- Can manage users and branches
- Can view all reports and analytics

### Doctor
- View patients and medical records
- Manage appointments and prescriptions
- Cannot access billing or inventory

### Nurse
- View patients and medical records
- Manage appointments
- Limited access to prescriptions

### Receptionist
- Manage patient registration
- Schedule appointments
- Cannot access medical records

### Pharmacist
- Manage medicine inventory
- View prescriptions
- Cannot access patient details

### Lab Technician
- Manage lab tests
- View patient test results
- Limited patient access

### Accountant
- Manage billing and invoices
- View financial reports
- Cannot access medical records

### IT Staff
- Manage user accounts
- System maintenance
- Limited access to patient data

## Rate Limiting

API requests are limited to 100 requests per minute per user.

## Error Handling

Always check the `success` field in responses. For errors, refer to the `error` field and `errors` object for detailed validation messages.

## Pagination

List endpoints support pagination using `page` and `limit` parameters. The response includes pagination metadata.

## Search and Filtering

Most list endpoints support search and filtering. Check individual endpoint documentation for available parameters.

## Branch Management

Multi-branch functionality is built-in. Non-admin users can only access data from their assigned branch.

## Security

- All requests are logged for audit purposes
- Session timeout: 30 minutes of inactivity
- CSRF protection enabled
- Input sanitization and validation
- SQL injection protection
- XSS protection
