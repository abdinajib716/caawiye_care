# Caawiye-Care

A comprehensive healthcare management system built with Laravel, featuring patient management, appointment scheduling, medical records, and administrative tools for healthcare providers.

## Features

### 🏥 **Healthcare Management**
- **Patient Management** - Complete patient registration and profile management
- **Appointment Scheduling** - Book, manage, and track appointments
- **Medical Records** - Digital health records and medical history
- **Doctor Management** - Healthcare provider profiles and specializations
- **Prescription Management** - Digital prescriptions and medication tracking

### 🔐 **Authentication & Security**
- **Role-Based Access Control** - Patients, Doctors, Nurses, Admins
- **Secure Authentication** - Login/logout with password reset
- **Permission Management** - Granular access control
- **Data Privacy** - HIPAA-compliant data handling

### 💼 **Administrative Features**
- **Dashboard** - Comprehensive analytics and insights
- **User Management** - Staff and patient account management
- **Settings Management** - System configuration
- **Reporting** - Medical reports and analytics
- **Audit Logs** - Complete activity tracking

### 🎨 **User Experience**
- **Responsive Design** - Works on all devices
- **Dark/Light Mode** - Theme customization
- **Multi-language Support** - Internationalization ready
- **Modern UI** - Clean and intuitive interface

## Technology Stack

- **Backend:** Laravel 12.x
- **Frontend:** Tailwind CSS 4.x, Alpine.js, Livewire 3.x
- **Database:** MySQL/PostgreSQL
- **Authentication:** Laravel Sanctum
- **Permissions:** Spatie Laravel Permission
- **Testing:** Pest PHP

## Requirements

- PHP 8.3 or higher
- Laravel 12.x
- MySQL 8.0+ or PostgreSQL 13+
- Node.js 18+ & NPM
- Composer 2.x

## Installation

1. Clone the repository
```bash
git clone https://github.com/abdinajib716/Caawiye-Care-.git
cd Caawiye-Care-
```

2. Install dependencies
```bash
composer install
npm install
```

3. Environment setup
```bash
cp .env.example .env
php artisan key:generate
```

4. Database setup
```bash
php artisan migrate:fresh --seed
php artisan module:seed
```

5. Storage link
```bash
php artisan storage:link
```

6. Run the application
```bash
composer run dev
```

## Default Login Credentials

### Super Admin
- **Email:** superadmin@example.com
- **Password:** 12345678

### Doctor
- **Email:** doctor@example.com
- **Password:** 12345678

### Patient
- **Email:** patient@example.com
- **Password:** 12345678

## User Roles

- **Super Admin** - Full system access and configuration
- **Admin** - Administrative functions and user management
- **Doctor** - Patient management, appointments, medical records
- **Nurse** - Patient care, appointment assistance
- **Patient** - Personal health records, appointment booking

## API Documentation

The system includes comprehensive REST API endpoints for:
- Patient management
- Appointment scheduling
- Medical records
- User authentication
- Healthcare provider operations

API documentation available at: `/docs/api`

## Security Features

- HIPAA-compliant data handling
- Encrypted patient data
- Audit logging for all medical records access
- Role-based access control
- Secure authentication with password policies
- Data backup and recovery systems

## Contributing

We welcome contributions to improve Caawiye-Care. Please follow our contribution guidelines and ensure all medical data handling complies with healthcare regulations.

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support and questions, please contact:
- Email: support@caawiyecare.com
- Documentation: [Project Wiki]
- Issues: [GitHub Issues]

---

**Note:** This system handles sensitive medical data. Ensure compliance with local healthcare regulations (HIPAA, GDPR, etc.) before deployment in production environments.
