# HMS API Documentation

## Authentication
All API endpoints require authentication via JWT tokens.

### Login Endpoint
```
POST /api/v1/auth/login
Content-Type: application/json

{
    "email": "admin@hospital.com",
    "password": "admin123"
}
```

### Response
```json
{
    "status": "success",
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "user": {
        "id": 1,
        "name": "Admin",
        "email": "admin@hospital.com",
        "role": "admin"
    }
}
```

## Patient Management

### Get Patients
```
GET /api/v1/patients
Authorization: Bearer {token}
```

### Create Patient
```
POST /api/v1/patients
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "1234567890",
    "address": "123 Main St",
    "date_of_birth": "1990-01-01"
}
```

## Appointment Management

### Get Appointments
```
GET /api/v1/appointments
Authorization: Bearer {token}
```

### Create Appointment
```
POST /api/v1/appointments
Authorization: Bearer {token}
Content-Type: application/json

{
    "patient_id": 1,
    "doctor_id": 1,
    "date": "2024-01-15",
    "time": "10:00",
    "notes": "Regular checkup"
}
```

## Error Handling
All errors return consistent format:
```json
{
    "status": "error",
    "message": "Error description",
    "code": 400
}
```
