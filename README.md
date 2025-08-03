# Altezza Property Management System

A comprehensive Laravel-based property management system designed for apartment rental management, featuring multi-role authentication, automated billing, maintenance tracking, and comprehensive reporting.

## Features

### 🏢 Master Data Management
- **Apartments**: Manage apartment units with details like number, block, floor, type, and status
- **Tenants**: Complete tenant profiles with lease information and documents
- **Owners**: Property owner management with bank details and contact information
- **User Management**: Multi-role user system (Admin, Manager, Tenant)

### 🔧 Operations Management
- **Contract Management**: Lease agreements, renewals, and expiry tracking
- **Maintenance Requests**: Tenant request logging, assignment, and resolution tracking
- **Complaint Management**: Complaint logging, follow-up, and resolution
- **Notices & Communication**: Broadcast messages, circulars, and payment reminders

### 💰 Financial Management
- **Rent Invoicing**: Automated monthly/periodic rent billing
- **Utility Billing**: Electricity, water, gas billing with meter readings
- **Maintenance Charges**: Recurring and ad-hoc maintenance billing
- **Payment Tracking**: Complete payment history with receipts
- **Outstanding Management**: Overdue tracking with automated reminders

### 📊 Reporting & Automation
- **Monthly Statements**: For tenants, owners, and management
- **Automated Reminders**: Payment due dates, lease renewals, overdue notifications
- **Export/Import**: Excel and PDF reports for all modules
- **Dashboard Analytics**: Role-based dashboards with key metrics

## Technology Stack

- **Framework**: Laravel 12.x
- **Authentication**: Laravel Breeze with multi-role support
- **Database**: MySQL with comprehensive relationships
- **Permissions**: Spatie Laravel-Permission
- **Export/Import**: Laravel Excel (Maatwebsite/Excel)
- **Frontend**: Blade Templates with responsive design
- **Styling**: Bootstrap/Tailwind CSS

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL 8.0 or higher

### Setup Instructions

1. **Install PHP dependencies**
   ```bash
   composer install
   ```

2. **Install JavaScript dependencies**
   ```bash
   npm install
   ```

3. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Configuration**
   Update your `.env` file with database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=altezza
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Run Migrations**
   ```bash
   php artisan migrate
   ```

6. **Build Assets**
   ```bash
   npm run build
   ```

7. **Start Development Server**
   ```bash
   php artisan serve
   ```

## User Roles & Permissions

### Admin
- Full system access
- User management
- System configuration
- All reports and analytics

### Manager
- Day-to-day operations
- Tenant and apartment management
- Invoice and payment processing
- Maintenance and complaint handling

### Tenant
- View personal dashboard
- Pay invoices online
- Raise maintenance requests
- Submit complaints
- View notices and communications

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
