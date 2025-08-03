# Altezza Property Management System - Copilot Instructions

<!-- Use this file to provide workspace-specific custom instructions to Copilot. For more details, visit https://code.visualstudio.com/docs/copilot/copilot-customization#_use-a-githubcopilotinstructionsmd-file -->

## Project Overview
This is a comprehensive Laravel-based property management system for apartment rentals with the following key features:

### Core Entities
- **Users**: Multi-role authentication (Admin, Manager, Tenant)
- **Owners**: Property owners with bank details and contact information
- **Apartments**: Property units with details like type, floor, block, status
- **Tenants**: Extended user profiles with lease information
- **Leases**: Rental agreements between owners, tenants, and apartments

### Modules
1. **Master Data Management**: Apartments, Tenants, Owners, User Management
2. **Operations**: Contract Management, Maintenance Requests, Complaint Management, Notices & Communication
3. **Payments & Invoicing**: Rent Invoicing, Utility Billing, Maintenance Charges, Payment Tracking, Outstanding Management
4. **Reporting & Automation**: Monthly Statements, Automated Reminders, Export/Import functionality

### Technical Stack
- **Framework**: Laravel 12.x
- **Authentication**: Laravel Breeze with multi-role support
- **Permissions**: Spatie Laravel-Permission package
- **Database**: MySQL with comprehensive foreign key relationships
- **Export/Import**: Laravel Excel (Maatwebsite/Excel)

### Key Features
- Multi-role dashboard (Admin/Manager/Tenant specific views)
- Automated invoice generation and payment tracking
- Maintenance request management with status tracking
- Utility meter readings and billing
- Notice board and communication system
- Comprehensive reporting with Excel/PDF exports
- Automated reminders for payments and lease renewals

### Database Structure
The system includes 12+ core tables with proper relationships:
- users (with role-based access)
- owners, apartments, tenants, leases
- invoices, payments, utility_meters, utility_readings
- maintenance_requests, complaints, notices, settings

### Development Guidelines
- Follow Laravel best practices and conventions
- Use Eloquent relationships for data access
- Implement proper authorization using roles and permissions
- Create responsive UI using Blade templates
- Add proper validation for all forms
- Include comprehensive error handling
- Write clean, documented code with proper type hints

### UI/UX Requirements
- Role-based dashboards with relevant widgets and statistics
- Clean, professional interface suitable for property management
- Mobile-responsive design for field staff and tenants
- Intuitive navigation with proper access controls
- Real-time notifications for important events
- Export capabilities for reports and statements

When working on this project, consider the multi-tenant nature, role-based access controls, and the need for automation in property management workflows.
