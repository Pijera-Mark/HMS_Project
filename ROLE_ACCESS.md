# HMS Role-Based Access Control

## Role Permissions Matrix

### 🏥 **Admin** (Full Access)
- **Dashboard**: ✅ Complete overview with all statistics
- **Patients**: ✅ Full patient management
- **Appointments**: ✅ All appointment management
- **Admissions**: ✅ Full admission control
- **Medical Records**: ✅ Complete access
- **Prescriptions**: ✅ Full prescription management
- **Lab Tests**: ✅ Complete lab management
- **Operations**: 
  - Medicine Inventory: ✅ Full control
  - Billing & Invoices: ✅ Complete billing
  - Ward Management: ✅ Full ward control
- **Administration**: 
  - Doctors: ✅ Full management
  - User Management: ✅ Complete control
  - Branch Management: ✅ Multi-branch support
  - System Settings: ✅ Full system control
- **Reports**: 
  - Financial Reports: ✅ Complete financial data
  - Patient Reports: ✅ All patient analytics
  - Audit Trail: ✅ System audit logs

### 👨‍⚕️ **Doctor** (Clinical Focus)
- **Dashboard**: ✅ Medical dashboard with patient stats
- **Patients**: ✅ View and manage assigned patients
- **Appointments**: ✅ Manage own appointments
- **Admissions**: ✅ Manage patient admissions
- **Medical Records**: ✅ Full access to patient records
- **Prescriptions**: ✅ Create and manage prescriptions
- **Lab Tests**: ✅ Order and view lab results
- **Operations**: ❌ No access
- **Administration**: ❌ No access
- **Reports**: 
  - Financial Reports: ❌ No access
  - Patient Reports: ✅ Patient analytics
  - Audit Trail: ❌ No access

### 👩‍⚕️ **Nurse** (Patient Care)
- **Dashboard**: ✅ Nursing dashboard
- **Patients**: ✅ Patient management and care
- **Appointments**: ✅ Appointment scheduling
- **Admissions**: ✅ Patient admission management
- **Medical Records**: ✅ View and update records
- **Prescriptions**: ❌ Cannot prescribe
- **Lab Tests**: ❌ Limited access
- **Operations**: 
  - Medicine Inventory: ❌ No access
  - Billing & Invoices: ❌ No access
  - Ward Management: ✅ Ward patient management
- **Administration**: ❌ No access
- **Reports**: ❌ No access

### 💊 **Pharmacist** (Medication Management)
- **Dashboard**: ✅ Pharmacy dashboard
- **Patients**: ❌ Limited access
- **Appointments**: ❌ No access
- **Admissions**: ❌ No access
- **Medical Records**: ✅ View for medication purposes
- **Prescriptions**: ✅ Dispense and manage
- **Lab Tests**: ❌ No access
- **Operations**: 
  - Medicine Inventory: ✅ Full inventory control
  - Billing & Invoices: ❌ No access
  - Ward Management: ❌ No access
- **Administration**: ❌ No access
- **Reports**: ❌ No access

### 👩‍💼 **Receptionist** (Front Desk)
- **Dashboard**: ✅ Reception dashboard
- **Patients**: ✅ Patient registration and management
- **Appointments**: ✅ Full appointment scheduling
- **Admissions**: ❌ Limited access
- **Medical Records**: ❌ No access
- **Prescriptions**: ❌ No access
- **Lab Tests**: ❌ No access
- **Operations**: 
  - Medicine Inventory: ❌ No access
  - Billing & Invoices: ✅ Invoice creation
  - Ward Management: ❌ No access
- **Administration**: ❌ No access
- **Reports**: ❌ No access

### 🧪 **Lab Staff** (Laboratory)
- **Dashboard**: ✅ Lab dashboard
- **Patients**: ❌ Limited access
- **Appointments**: ❌ No access
- **Admissions**: ❌ No access
- **Medical Records**: ❌ No access
- **Prescriptions**: ❌ No access
- **Lab Tests**: ✅ Complete lab management
- **Operations**: ❌ No access
- **Administration**: ❌ No access
- **Reports**: ❌ No access

### 💰 **Accountant** (Financial)
- **Dashboard**: ✅ Financial dashboard
- **Patients**: ❌ Limited access
- **Appointments**: ❌ No access
- **Admissions**: ❌ No access
- **Medical Records**: ❌ No access
- **Prescriptions**: ❌ No access
- **Lab Tests**: ❌ No access
- **Operations**: 
  - Medicine Inventory: ❌ No access
  - Billing & Invoices: ✅ Complete billing management
  - Ward Management: ❌ No access
- **Administration**: ❌ No access
- **Reports**: 
  - Financial Reports: ✅ Complete financial reports
  - Patient Reports: ❌ No access
  - Audit Trail: ❌ No access

## 🎯 **Key Features**

### **Dynamic Menu Display**
- Menu sections only appear if user has access
- Empty sections are automatically hidden
- Clean, role-appropriate navigation

### **Security Implementation**
- Server-side role validation
- Client-side menu filtering
- Route protection by role

### **User Experience**
- Role-specific dashboards
- Relevant statistics only
- Streamlined workflow per role

## 🔧 **Technical Implementation**

### **Helper Functions**
- `canAccess()` - General role checking
- `isAdmin()` - Admin verification
- Role-specific functions for each module

### **Menu Structure**
- Main Navigation (Core features)
- Operations (Management tools)
- Administration (System controls)
- Reports (Analytics)
- System (Personal & help)

### **Access Control**
- PHP-based role checking
- Conditional menu rendering
- Secure permission validation
