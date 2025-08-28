<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Altezza Property Management - Complete A-Z Data Entry User Flow</title>
    <style>
        @page {
            size: A4;
            margin: 0.75in;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #2d3748;
            background: #ffffff;
        }
        
        .document-container {
            max-width: 100%;
            margin: 0 auto;
        }
        
        /* Header Styling */
        .document-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #4f46e5;
        }
        
        .main-title {
            font-size: 24px;
            font-weight: bold;
            color: #4f46e5;
            margin-bottom: 8px;
        }
        
        .subtitle {
            font-size: 16px;
            color: #6b7280;
            margin-bottom: 15px;
        }
        
        .document-info {
            font-size: 10px;
            color: #9ca3af;
        }
        
        /* Table of Contents */
        .toc {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        
        .toc-title {
            font-size: 16px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 15px;
        }
        
        .toc-list {
            list-style: none;
        }
        
        .toc-item {
            margin-bottom: 5px;
            padding-left: 10px;
        }
        
        .toc-number {
            color: #4f46e5;
            font-weight: 600;
        }
        
        /* Section Styling */
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 15px;
            padding: 10px 0;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .phase-title {
            font-size: 16px;
            font-weight: bold;
            color: #4f46e5;
            margin-bottom: 12px;
            margin-top: 20px;
        }
        
        .step-title {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            margin-top: 15px;
        }
        
        /* Content Styling */
        .overview-box {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #4f46e5;
            margin-bottom: 20px;
        }
        
        .info-box {
            background: #f0fdf4;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #10b981;
            margin: 10px 0;
        }
        
        .warning-box {
            background: #fef3c7;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #f59e0b;
            margin: 10px 0;
        }
        
        .code-block {
            background: #f3f4f6;
            padding: 12px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 10px;
            margin: 8px 0;
            border: 1px solid #e5e7eb;
        }
        
        /* Lists */
        .workflow-list {
            margin: 10px 0;
            padding-left: 20px;
        }
        
        .workflow-list li {
            margin-bottom: 5px;
        }
        
        .requirements-list {
            background: #f9fafb;
            padding: 15px;
            border-radius: 6px;
            margin: 10px 0;
        }
        
        /* Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 10px;
        }
        
        .data-table th {
            background: #4f46e5;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: 600;
        }
        
        .data-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .data-table tbody tr:nth-child(even) {
            background: #f9fafb;
        }
        
        /* Status badges */
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: 600;
        }
        
        .status-required {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .status-optional {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .status-admin {
            background: #fef3c7;
            color: #92400e;
        }
        
        /* Navigation info */
        .nav-info {
            background: #ede9fe;
            padding: 10px;
            border-radius: 4px;
            margin: 8px 0;
            font-size: 10px;
        }
        
        .route-info {
            color: #7c3aed;
            font-family: 'Courier New', monospace;
            font-weight: 600;
        }
        
        /* Footer */
        .document-footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
        }
        
        /* Page breaks */
        .page-break {
            page-break-before: always;
        }
        
        .no-break {
            page-break-inside: avoid;
        }
        
        /* Print specific */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="document-container">
        <!-- Document Header -->
        <div class="document-header">
            <h1 class="main-title">🏢 ALTEZZA PROPERTY MANAGEMENT SYSTEM</h1>
            <h2 class="subtitle">Complete A-Z Data Entry User Flow Guide</h2>
            <div class="document-info">
                <p>Generated on: {{ now()->format('F d, Y \a\t g:i A') }}</p>
                <p>Version: 1.0 | System Build: Laravel 12.x</p>
                <p>Document Type: Administrative Workflow Guide</p>
            </div>
        </div>

        <!-- Table of Contents -->
        <div class="toc">
            <h3 class="toc-title">📋 Table of Contents</h3>
            <ol class="toc-list">
                <li class="toc-item"><span class="toc-number">1.</span> System Overview & Architecture</li>
                <li class="toc-item"><span class="toc-number">2.</span> Initial System Access (Phase 1)</li>
                <li class="toc-item"><span class="toc-number">3.</span> Master Data Setup (Phase 2)</li>
                <li class="toc-item"><span class="toc-number">4.</span> Lease Management (Phase 3)</li>
                <li class="toc-item"><span class="toc-number">5.</span> Utility System Setup (Phase 4)</li>
                <li class="toc-item"><span class="toc-number">6.</span> Financial Operations (Phase 5)</li>
                <li class="toc-item"><span class="toc-number">7.</span> Expense Management (Phase 6)</li>
                <li class="toc-item"><span class="toc-number">8.</span> Operations Management (Phase 7)</li>
                <li class="toc-item"><span class="toc-number">9.</span> System Configuration (Phase 8)</li>
                <li class="toc-item"><span class="toc-number">10.</span> Owner Workflow (Phase 9)</li>
                <li class="toc-item"><span class="toc-number">11.</span> Reporting & Analytics (Phase 10)</li>
                <li class="toc-item"><span class="toc-number">12.</span> Recommended Workflows & Best Practices</li>
            </ol>
        </div>

        <!-- Section 1: System Overview -->
        <div class="section">
            <h2 class="section-title">1. System Overview & Architecture</h2>
            
            <div class="overview-box">
                <h3 class="step-title">🏗️ Technical Architecture</h3>
                <ul class="workflow-list">
                    <li><strong>Framework:</strong> Laravel 12.x with MVC architecture</li>
                    <li><strong>Authentication:</strong> Laravel Breeze with multi-role support</li>
                    <li><strong>Database:</strong> MySQL with comprehensive foreign key relationships</li>
                    <li><strong>UI Framework:</strong> Blade templates with Tailwind CSS</li>
                    <li><strong>PDF Generation:</strong> DOMPDF for invoices and reports</li>
                    <li><strong>Real-time Features:</strong> Notification system with AJAX polling</li>
                </ul>
            </div>

            <div class="info-box">
                <h3 class="step-title">👥 User Roles & Access Levels</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Role</th>
                            <th>Access Level</th>
                            <th>Primary Functions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="status-badge status-admin">Admin</span></td>
                            <td>Full System Access</td>
                            <td>User management, financial oversight, system configuration</td>
                        </tr>
                        <tr>
                            <td><span class="status-badge status-required">Manager</span></td>
                            <td>Operations & Management</td>
                            <td>Property operations, owner management, approvals</td>
                        </tr>
                        <tr>
                            <td><span class="status-badge status-optional">Owner</span></td>
                            <td>Personal Dashboard</td>
                            <td>Invoices, maintenance requests, personal information</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="warning-box">
                <h3 class="step-title">⚡ Current System Status</h3>
                <ul class="workflow-list">
                    <li><strong>Default Users:</strong> admin@altezza.com / password & manager@altezza.com / password</li>
                    <li><strong>Database Status:</strong> Clean system with 0 records (ready for data entry)</li>
                    <li><strong>URL:</strong> http://127.0.0.1:8000</li>
                </ul>
            </div>
        </div>

        <!-- Page Break -->
        <div class="page-break"></div>

        <!-- Section 2: Phase 1 -->
        <div class="section">
            <h2 class="section-title">2. Phase 1: Initial System Access</h2>
            
            <div class="phase-title">🎯 Step A1: System Login</div>
            
            <div class="nav-info">
                <strong>🌐 URL:</strong> <span class="route-info">http://127.0.0.1:8000/login</span><br>
                <strong>🔑 Default Credentials:</strong><br>
                • Admin: admin@altezza.com / password<br>
                • Manager: manager@altezza.com / password
            </div>

            <div class="requirements-list">
                <h4 class="step-title">Process Flow:</h4>
                <ol class="workflow-list">
                    <li>Open web browser → Navigate to application URL</li>
                    <li>Enter admin/manager credentials → Click "Login"</li>
                    <li>System authenticates → Redirects to role-based dashboard</li>
                    <li>Dashboard displays system overview with statistics</li>
                </ol>
            </div>
        </div>

        <!-- Section 3: Phase 2 -->
        <div class="section">
            <h2 class="section-title">3. Phase 2: Master Data Setup (Admin/Manager)</h2>
            
            <div class="phase-title">🏗️ Step A2: Create Property Owners</div>
            
            <div class="nav-info">
                <strong>📍 Navigation:</strong> Dashboard → Master Data → Owners → Add Owner<br>
                <strong>🔗 Route:</strong> <span class="route-info">/owners/create</span>
            </div>

            <div class="requirements-list">
                <h4 class="step-title">Required Data Fields:</h4>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Field Category</th>
                            <th>Field Name</th>
                            <th>Status</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td rowspan="5">Personal Information</td>
                            <td>Full Name</td>
                            <td><span class="status-badge status-required">Required</span></td>
                            <td>Must be unique</td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td><span class="status-badge status-required">Required</span></td>
                            <td>Must be unique</td>
                        </tr>
                        <tr>
                            <td>Phone</td>
                            <td><span class="status-badge status-optional">Optional</span></td>
                            <td>Contact number</td>
                        </tr>
                        <tr>
                            <td>Address</td>
                            <td><span class="status-badge status-optional">Optional</span></td>
                            <td>Complete address</td>
                        </tr>
                        <tr>
                            <td>ID Document</td>
                            <td><span class="status-badge status-optional">Optional</span></td>
                            <td>Government ID</td>
                        </tr>
                        <tr>
                            <td rowspan="4">Bank Details</td>
                            <td>Account Name</td>
                            <td><span class="status-badge status-optional">Optional</span></td>
                            <td>For rent collection</td>
                        </tr>
                        <tr>
                            <td>Account Number</td>
                            <td><span class="status-badge status-optional">Optional</span></td>
                            <td>Bank account</td>
                        </tr>
                        <tr>
                            <td>Bank Name</td>
                            <td><span class="status-badge status-optional">Optional</span></td>
                            <td>Financial institution</td>
                        </tr>
                        <tr>
                            <td>Routing Number</td>
                            <td><span class="status-badge status-optional">Optional</span></td>
                            <td>Bank routing code</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="code-block">
Example Data:
Name: John Smith
Email: john.smith@email.com
Phone: +1-234-567-8901
Address: 123 Main Street, City, State 12345
ID Document: DL123456789
Bank Account Name: John Smith
Account Number: 1234567890
Bank Name: First National Bank
Routing Number: 123456789
Status: Active
            </div>

            <!-- Continue with Step A3 -->
            <div class="phase-title">🏠 Step A3: Create Apartments/Units</div>
            
            <div class="nav-info">
                <strong>📍 Navigation:</strong> Dashboard → Master Data → Apartments → Add Apartment<br>
                <strong>🔗 Route:</strong> <span class="route-info">/apartments/create</span>
            </div>

            <div class="requirements-list">
                <h4 class="step-title">Required Data Fields:</h4>
                <ul class="workflow-list">
                    <li><strong>Physical Info:</strong> Number* (required), Block, Floor, Type (1BHK/2BHK/3BHK/4BHK/studio/penthouse)</li>
                    <li><strong>Details:</strong> Area (sq ft), Rent Amount, Description</li>
                    <li><strong>Relationships:</strong> Owner Assignment* (required), Status (vacant/occupied/maintenance)</li>
                </ul>
            </div>

            <div class="info-box">
                <h4 class="step-title">Workflow Process:</h4>
                <ol class="workflow-list">
                    <li>Navigate to apartments section → Click "Add Apartment"</li>
                    <li>Enter apartment physical details (number, assessment no)</li>
                    <li>Assign to previously created owner</li>
                    <li>Set initial status as "vacant"</li>
                    <li>System creates apartment → Links to owner</li>
                </ol>
            </div>

            <!-- Step A4 -->
            <div class="phase-title">👤 Step A4: Create Owner Users</div>
            
            <div class="nav-info">
                <strong>📍 Navigation:</strong> Dashboard → Master Data → Owners → Add Owner<br>
                <strong>🔗 Route:</strong> <span class="route-info">/owners/create</span>
            </div>

            <div class="requirements-list">
                <h4 class="step-title">Required Data Fields:</h4>
                <ul class="workflow-list">
                    <li><strong>Account Info:</strong> Full Name*, Email*, Password*, Phone</li>
                    <li><strong>Personal Details:</strong> Address, ID Document, Date of Birth</li>
                    <li><strong>Emergency Contact:</strong> Name*, Phone*, Relationship</li>
                    <li><strong>System:</strong> Role (auto-set to owner), Status (active)</li>
                </ul>
            </div>
        </div>

        <!-- Page Break -->
        <div class="page-break"></div>

        <!-- Section 4: Phase 3 -->
        <div class="section">
            <h2 class="section-title">4. Phase 3: Lease Management</h2>
            
            <div class="phase-title">📋 Step A5: Create Lease Agreements</div>
            
            <div class="nav-info">
                <strong>📍 Navigation:</strong> Dashboard → Operations → Leases → Create Lease<br>
                <strong>🔗 Route:</strong> <span class="route-info">/leases/create</span>
            </div>

            <div class="requirements-list">
                <h4 class="step-title">Required Data Fields:</h4>
                <ul class="workflow-list">
                    <li><strong>Parties:</strong> Owner Selection*, Apartment Assignment*, Owner (auto-populated)</li>
                    <li><strong>Terms:</strong> Start Date*, End Date*, Monthly Rent*, Security Deposit</li>
                    <li><strong>Details:</strong> Lease Number (auto-generated), Special Terms, Status</li>
                </ul>
            </div>

            <div class="info-box">
                <h4 class="step-title">Critical Workflow Process:</h4>
                <ol class="workflow-list">
                    <li>Navigate to lease management → "Create Lease"</li>
                    <li>Select owner from dropdown (created in Step A4)</li>
                    <li>Select apartment from available units (created in Step A3)</li>
                    <li>Enter lease terms and dates</li>
                    <li>System creates lease → Updates apartment status to "occupied"</li>
                    <li><strong>Establishes owner-apartment relationship through active lease</strong></li>
                </ol>
            </div>
        </div>

        <!-- Section 5: Phase 4 -->
        <div class="section">
            <h2 class="section-title">5. Phase 4: Utility System Setup</h2>
            
            <div class="phase-title">⚡ Step A6: Setup Utility Meters</div>
            
            <div class="nav-info">
                <strong>📍 Navigation:</strong> Dashboard → Utilities → Meters → Add Meter<br>
                <strong>🔗 Route:</strong> <span class="route-info">/utility-meters/create</span>
            </div>

            <div class="requirements-list">
                <h4 class="step-title">Required Data Fields:</h4>
                <ul class="workflow-list">
                    <li><strong>Meter Info:</strong> Meter Number*, Type (electricity/water/gas), Location</li>
                    <li><strong>Assignment:</strong> Apartment*, Status (active/inactive)</li>
                    <li><strong>Technical:</strong> Unit of Measurement, Last Reading Date</li>
                </ul>
            </div>

            <div class="phase-title">💰 Step A7: Configure Utility Pricing</div>
            
            <div class="nav-info">
                <strong>📍 Navigation:</strong> Dashboard → Utilities → Unit Prices → Add Price<br>
                <strong>🔗 Route:</strong> <span class="route-info">/utility-unit-prices/create</span>
            </div>

            <div class="requirements-list">
                <h4 class="step-title">Required Data Fields:</h4>
                <ul class="workflow-list">
                    <li><strong>Pricing:</strong> Utility Type*, Price per Unit*, Effective Date*</li>
                    <li><strong>Optional:</strong> Tier-based pricing, Special rates</li>
                </ul>
            </div>
        </div>

        <!-- Section 6: Phase 5 -->
        <div class="section">
            <h2 class="section-title">6. Phase 5: Financial Operations</h2>
            
            <div class="phase-title">📄 Step A8: Generate Invoices</div>
            
            <div class="nav-info">
                <strong>📍 Navigation:</strong> Dashboard → Financial → Invoices → Create Invoice<br>
                <strong>🔗 Route:</strong> <span class="route-info">/invoices/create</span>
            </div>

            <div class="info-box">
                <h4 class="step-title">Invoice Creation Options:</h4>
                <ul class="workflow-list">
                    <li><strong>Manual Invoice:</strong> Individual owner billing</li>
                    <li><strong>Bulk Generation:</strong> Monthly rent for all active leases</li>
                </ul>
            </div>

            <div class="requirements-list">
                <h4 class="step-title">Required Data Fields:</h4>
                <ul class="workflow-list">
                    <li><strong>Invoice Details:</strong> Type (rent/utility/other), Amount*, Due Date*</li>
                    <li><strong>Assignment:</strong> Owner*, Apartment*, Billing Period</li>
                    <li><strong>Additional:</strong> Description, Line Items, Discount, Late Fees</li>
                </ul>
            </div>

            <div class="phase-title">📊 Step A9: Record Utility Readings</div>
            
            <div class="nav-info">
                <strong>📍 Navigation:</strong> Dashboard → Utilities → Readings → Add Reading<br>
                <strong>🔗 Route:</strong> <span class="route-info">/utility-readings/create</span>
            </div>

            <div class="phase-title">💵 Step A10: Process Payments</div>
            
            <div class="nav-info">
                <strong>📍 Navigation:</strong> Dashboard → Financial → Payments → Create Payment<br>
                <strong>🔗 Route:</strong> <span class="route-info">/payments/create</span>
            </div>

            <div class="requirements-list">
                <h4 class="step-title">Required Data Fields:</h4>
                <ul class="workflow-list">
                    <li><strong>Payment Info:</strong> Invoice Selection*, Amount*, Payment Date*</li>
                    <li><strong>Details:</strong> Payment Method, Reference Number, Notes</li>
                    <li><strong>Banking:</strong> Transaction details, Receipt upload</li>
                </ul>
            </div>
        </div>

        <!-- Page Break -->
        <div class="page-break"></div>

        <!-- Section 7: Operations Management -->
        <div class="section">
            <h2 class="section-title">7. Phase 7: Operations Management</h2>
            
            <div class="phase-title">🔧 Step A13: Handle Maintenance Requests</div>
            
            <div class="nav-info">
                <strong>📍 Navigation:</strong> Dashboard → Operations → Maintenance → Create Request<br>
                <strong>🔗 Route:</strong> <span class="route-info">/maintenance-requests/create</span>
            </div>

            <div class="info-box">
                <h4 class="step-title">Request Sources:</h4>
                <ul class="workflow-list">
                    <li><strong>Admin/Manager Creation:</strong> For preventive maintenance</li>
                    <li><strong>Owner Submission:</strong> Through owner portal</li>
                </ul>
            </div>

            <div class="phase-title">🏢 Step A15: Create Rooftop Reservations</div>
            
            <div class="nav-info">
                <strong>📍 Navigation:</strong> Dashboard → Operations → Rooftop Reservations → Create Reservation<br>
                <strong>🔗 Route:</strong> <span class="route-info">/rooftop-reservations/create</span>
            </div>

            <div class="requirements-list">
                <h4 class="step-title">Required Data Fields:</h4>
                <ul class="workflow-list">
                    <li><strong>Event Info:</strong> Owner*, Event Date*, Time Slot*, Event Type*</li>
                    <li><strong>Details:</strong> Guest Count, Equipment Needs, Special Requirements</li>
                    <li><strong>Billing:</strong> Hourly Rate, Additional Charges, Security Deposit</li>
                </ul>
            </div>
        </div>

        <!-- Section 8: Owner Workflow -->
        <div class="section">
            <h2 class="section-title">9. Phase 9: Owner Workflow</h2>
            
            <div class="phase-title">👤 Owner Login & Self-Service</div>
            
            <div class="nav-info">
                <strong>🌐 Owner URL:</strong> <span class="route-info">http://127.0.0.1:8000/login</span><br>
                <strong>🔑 Credentials:</strong> owner-email@domain.com / assigned-password
            </div>

            <div class="requirements-list">
                <h4 class="step-title">Owner Capabilities:</h4>
                <ol class="workflow-list">
                    <li><strong>Dashboard Access:</strong> View apartment info, pending invoices</li>
                    <li><strong>Invoice Management:</strong> Check bills, download PDFs, view payment history</li>
                    <li><strong>Maintenance Requests:</strong> Submit new requests, track status</li>
                    <li><strong>Complaints:</strong> File grievances, monitor resolution</li>
                    <li><strong>Notices:</strong> Read announcements, acknowledge important notices</li>
                    <li><strong>Profile Management:</strong> Update contact information, emergency contacts</li>
                </ol>
            </div>
        </div>

        <!-- Section 9: Recommended Workflows -->
        <div class="section">
            <h2 class="section-title">12. Recommended Data Entry Sequence & Best Practices</h2>
            
            <div class="info-box">
                <h3 class="step-title">🚀 For New Property Setup</h3>
                <div class="code-block">
Sequence: Owners → Apartments → Owners → Leases → Utilities → Financial Operations
Timeline: 1-2 days for complete setup
Reasoning: Each step builds upon the previous ones, ensuring proper relationships
                </div>
            </div>

            <div class="warning-box">
                <h3 class="step-title">📅 Monthly Operations Cycle</h3>
                <ul class="workflow-list">
                    <li><strong>1st of Month:</strong> Utility readings</li>
                    <li><strong>5th of Month:</strong> Generate bills and invoices</li>
                    <li><strong>Throughout Month:</strong> Process payments, handle operations</li>
                    <li><strong>End of Month:</strong> Review and approve vouchers</li>
                </ul>
            </div>

            <div class="overview-box">
                <h3 class="step-title">⚡ Daily Operations</h3>
                <ul class="workflow-list">
                    <li>Monitor new maintenance requests</li>
                    <li>Process incoming payments</li>
                    <li>Review owner communications</li>
                    <li>Update system statuses</li>
                    <li>Handle urgent issues</li>
                </ul>
            </div>

            <div class="requirements-list">
                <h3 class="step-title">📈 Key Success Metrics</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Success Indicator</th>
                            <th>Verification Method</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Data Integrity</td>
                            <td>All relationships properly established</td>
                            <td>Check apartment-owner-lease connections</td>
                        </tr>
                        <tr>
                            <td>User Access</td>
                            <td>Role-based permissions working</td>
                            <td>Test login for each user type</td>
                        </tr>
                        <tr>
                            <td>Financial Flow</td>
                            <td>Invoice → Payment → Recording cycle</td>
                            <td>Complete transaction end-to-end</td>
                        </tr>
                        <tr>
                            <td>Operations</td>
                            <td>Maintenance and complaints tracking</td>
                            <td>Submit and track request status</td>
                        </tr>
                        <tr>
                            <td>Communication</td>
                            <td>Notices and notifications system</td>
                            <td>Send and receive system notifications</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Document Footer -->
        <div class="document-footer">
            <div style="margin-bottom: 10px;">
                <strong>🎉 System Status:</strong> Ready for Production Use
            </div>
            <div style="margin-bottom: 10px;">
                <strong>📧 Support Contact:</strong> admin@altezza.com | <strong>🌐 Documentation:</strong> System Help Section
            </div>
            <div>
                <strong>Document Generated:</strong> {{ now()->format('F d, Y \a\t g:i A') }} | 
                <strong>Version:</strong> Altezza Property Management v1.0 | 
                <strong>Total Pages:</strong> Multi-page comprehensive guide
            </div>
        </div>
    </div>
</body>
</html>
