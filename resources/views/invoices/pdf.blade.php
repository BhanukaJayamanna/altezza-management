<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }} - Altezza Property Management</title>
    <style>
        @page {
            size: A4;
            margin: 0.4in 0.6in;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #1f2937;
            background: #ffffff;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            position: relative;
            padding: 0 8px;
        }
        
        /* Modern Header Section */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 32px;
            padding: 24px 0;
            border-bottom: 1px solid #e5e7eb;
            position: relative;
        }
        
        .invoice-header::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #4f46e5 0%, #7c3aed 100%);
            border-radius: 2px;
        }
        
        .company-info {
            flex: 1;
            max-width: 350px;
        }
        
        .company-logo {
            display: flex;
            align-items: center;
            margin-bottom: 16px;
        }
        
        .logo-icon {
            width: 48px;
            height: 48px;
            margin-right: 16px;
            filter: drop-shadow(0 2px 4px rgba(79, 70, 229, 0.1));
        }
        
        .company-name {
            font-size: 30px;
            font-weight: 800;
            color: #4f46e5;
            letter-spacing: -0.8px;
            line-height: 1;
        }
        
        .company-tagline {
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
            margin-left: 64px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .company-address {
            margin-left: 64px;
            margin-top: 12px;
            color: #6b7280;
            font-size: 10px;
            line-height: 1.4;
        }
        
        .invoice-meta {
            text-align: right;
            flex: 0 0 auto;
            min-width: 220px;
        }
        
        .invoice-title {
            font-size: 36px;
            font-weight: 900;
            color: #111827;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        
        .invoice-number {
            font-size: 18px;
            color: #4f46e5;
            font-weight: 700;
            margin-bottom: 16px;
            background: #f8fafc;
            padding: 6px 12px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
        
        .invoice-dates {
            font-size: 10px;
            color: #6b7280;
            background: #f9fafb;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
        
        .invoice-dates div {
            margin-bottom: 6px;
            display: flex;
            justify-content: space-between;
        }
        
        .invoice-dates div:last-child {
            margin-bottom: 0;
        }
        
        .invoice-dates strong {
            color: #374151;
            min-width: 70px;
        }
        
        /* Modern Bill To Section */
        .billing-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 32px;
            gap: 24px;
        }
        
        .bill-to, .property-info {
            flex: 1;
        }
        
        .section-title {
            font-size: 11px;
            font-weight: 700;
            color: #4f46e5;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 4px;
            display: inline-block;
        }
        
        .owner-info, .property-details {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            position: relative;
        }
        
        .owner-info::before, .property-details::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #4f46e5 0%, #7c3aed 100%);
            border-radius: 12px 0 0 12px;
        }
        
        .owner-name {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 8px;
        }
        
        .owner-details, .property-details {
            font-size: 10px;
            color: #6b7280;
            line-height: 1.5;
        }
        
        /* Modern Invoice Items Table */
        .invoice-items {
            margin-bottom: 28px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
        }
        
        .items-table thead {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
        }
        
        .items-table th {
            padding: 16px 12px;
            text-align: left;
            font-weight: 700;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .items-table th:last-child {
            border-right: none;
        }
        
        .items-table td {
            padding: 16px 12px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 10px;
            vertical-align: top;
        }
        
        .items-table tbody tr {
            transition: background-color 0.2s ease;
        }
        
        .items-table tbody tr:nth-child(even) {
            background: #f9fafb;
        }
        
        .items-table tbody tr:hover {
            background: #f3f4f6;
        }
        
        .items-table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .amount-cell {
            text-align: right;
            font-weight: 700;
            color: #111827;
            font-family: 'SF Mono', 'Monaco', 'Consolas', monospace;
        }
        
        /* Modern Summary Section */
        .invoice-summary {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 32px;
        }
        
        .summary-table {
            width: 320px;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
        }
        
        .summary-table td {
            padding: 12px 16px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 11px;
        }
        
        .summary-table tr:last-child td {
            border-bottom: none;
        }
        
        .summary-table .label {
            text-align: left;
            color: #6b7280;
            font-weight: 600;
            width: 60%;
        }
        
        .summary-table .value {
            text-align: right;
            color: #111827;
            font-weight: 700;
            font-family: 'SF Mono', 'Monaco', 'Consolas', monospace;
            width: 40%;
        }
        
        .summary-total {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%) !important;
            color: white !important;
            font-weight: 800 !important;
            font-size: 14px !important;
        }
        
        .summary-total .label,
        .summary-total .value {
            color: white !important;
            font-weight: 800 !important;
        }
        
        /* Modern Payment Terms & Footer */
        .payment-terms {
            background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
            padding: 24px;
            border-radius: 12px;
            border: 1px solid #d1fae5;
            margin-bottom: 32px;
            position: relative;
        }
        
        .payment-terms::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #10b981 0%, #059669 100%);
            border-radius: 12px 0 0 12px;
        }
        
        .terms-title {
            font-size: 13px;
            font-weight: 700;
            color: #059669;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
        }
        
        .terms-title::before {
            content: '💳';
            margin-right: 8px;
            font-size: 16px;
        }
        
        .terms-content {
            font-size: 10px;
            color: #374151;
            line-height: 1.6;
        }
        
        .payment-methods {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #d1fae5;
        }
        
        .payment-method {
            display: block;
            margin-bottom: 4px;
            padding-left: 12px;
            position: relative;
        }
        
        .payment-method::before {
            content: '•';
            position: absolute;
            left: 0;
            color: #10b981;
            font-weight: bold;
        }
        
        .invoice-footer {
            text-align: center;
            padding: 24px 0;
            border-top: 2px solid #f3f4f6;
            font-size: 9px;
            color: #9ca3af;
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
            border-radius: 8px;
            margin-top: 16px;
        }
        
        .contact-info {
            margin-bottom: 12px;
            font-weight: 600;
        }
        
        .contact-info span {
            margin: 0 12px;
        }
        
        .footer-tagline {
            font-size: 8px;
            color: #d1d5db;
            margin-top: 8px;
        }
        
        /* Modern Status Badge */
        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 24px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            border: 2px solid transparent;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .status-paid {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border-color: #34d399;
        }
        
        .status-pending {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            border-color: #f59e0b;
        }
        
        .status-overdue {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border-color: #ef4444;
        }
        
        .status-partial {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
            border-color: #3b82f6;
        }
        
        /* Modern Watermark */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 120px;
            font-weight: 900;
            color: rgba(239, 68, 68, 0.08);
            z-index: -1;
            pointer-events: none;
            text-shadow: 0 0 20px rgba(239, 68, 68, 0.1);
            letter-spacing: 8px;
        }
        
        /* Enhanced Print Styles */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .invoice-container {
                box-shadow: none;
                margin: 0;
                padding: 0;
            }
            
            .invoice-header::after,
            .owner-info::before,
            .property-details::before,
            .payment-terms::before {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
        
        /* Modern Utility Classes */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: 700; }
        .font-semibold { font-weight: 600; }
        .font-medium { font-weight: 500; }
        .mb-1 { margin-bottom: 4px; }
        .mb-2 { margin-bottom: 8px; }
        .mb-3 { margin-bottom: 12px; }
        .mb-4 { margin-bottom: 16px; }
        .mt-2 { margin-top: 8px; }
        .mt-3 { margin-top: 12px; }
        .px-2 { padding-left: 8px; padding-right: 8px; }
        .py-1 { padding-top: 4px; padding-bottom: 4px; }
        
        /* Responsive adjustments for smaller screens */
        @media (max-width: 600px) {
            .invoice-container {
                padding: 0 4px;
            }
            
            .billing-section {
                flex-direction: column;
                gap: 16px;
            }
            
            .invoice-header {
                flex-direction: column;
                text-align: center;
            }
            
            .invoice-meta {
                text-align: center;
                margin-top: 16px;
            }
            
            .company-tagline,
            .company-address {
                margin-left: 0;
                text-align: center;
            }
            
            .items-table th,
            .items-table td {
                padding: 8px 6px;
                font-size: 9px;
            }
            
            .summary-table {
                width: 100%;
            }
        }
        
        /* Animation for interactive elements */
        .status-badge,
        .invoice-number {
            transition: all 0.3s ease;
        }
        
        .items-table tbody tr {
            transition: background-color 0.2s ease;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Watermark for unpaid invoices -->
        @if($invoice->status !== 'paid')
            <div class="watermark">
                @if($invoice->status === 'overdue')
                    OVERDUE
                @elseif($invoice->status === 'pending')
                    PENDING
                @else
                    UNPAID
                @endif
            </div>
        @endif

        <!-- Header Section -->
        <div class="invoice-header">
            <div class="company-info">
                <div class="company-logo">
                    <!-- Altezza Logo SVG -->
                    <svg class="logo-icon" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Main building structure -->
                        <rect x="20" y="25" width="60" height="70" fill="#4F46E5" rx="2"/>
                        <!-- Building levels -->
                        <rect x="22" y="27" width="56" height="2" fill="white" opacity="0.3"/>
                        <rect x="22" y="35" width="56" height="2" fill="white" opacity="0.3"/>
                        <rect x="22" y="43" width="56" height="2" fill="white" opacity="0.3"/>
                        <rect x="22" y="51" width="56" height="2" fill="white" opacity="0.3"/>
                        <rect x="22" y="59" width="56" height="2" fill="white" opacity="0.3"/>
                        <rect x="22" y="67" width="56" height="2" fill="white" opacity="0.3"/>
                        
                        <!-- Windows -->
                        <rect x="25" y="30" width="6" height="4" fill="white" opacity="0.8"/>
                        <rect x="35" y="30" width="6" height="4" fill="white" opacity="0.8"/>
                        <rect x="45" y="30" width="6" height="4" fill="white" opacity="0.8"/>
                        <rect x="55" y="30" width="6" height="4" fill="white" opacity="0.8"/>
                        <rect x="65" y="30" width="6" height="4" fill="white" opacity="0.8"/>
                        
                        <rect x="25" y="38" width="6" height="4" fill="white" opacity="0.8"/>
                        <rect x="35" y="38" width="6" height="4" fill="white" opacity="0.8"/>
                        <rect x="45" y="38" width="6" height="4" fill="white" opacity="0.8"/>
                        <rect x="55" y="38" width="6" height="4" fill="white" opacity="0.8"/>
                        <rect x="65" y="38" width="6" height="4" fill="white" opacity="0.8"/>
                        
                        <rect x="25" y="46" width="6" height="4" fill="white" opacity="0.8"/>
                        <rect x="35" y="46" width="6" height="4" fill="white" opacity="0.8"/>
                        <rect x="45" y="46" width="6" height="4" fill="white" opacity="0.8"/>
                        <rect x="55" y="46" width="6" height="4" fill="white" opacity="0.8"/>
                        <rect x="65" y="46" width="6" height="4" fill="white" opacity="0.8"/>
                        
                        <rect x="25" y="54" width="6" height="4" fill="white" opacity="0.8"/>
                        <rect x="35" y="54" width="6" height="4" fill="white" opacity="0.8"/>
                        <rect x="45" y="54" width="6" height="4" fill="white" opacity="0.8"/>
                        <rect x="55" y="54" width="6" height="4" fill="white" opacity="0.8"/>
                        <rect x="65" y="54" width="6" height="4" fill="white" opacity="0.8"/>
                        
                        <!-- Entrance -->
                        <rect x="40" y="75" width="20" height="20" fill="white" opacity="0.9"/>
                        <rect x="42" y="77" width="16" height="16" fill="#4F46E5"/>
                        
                        <!-- Rooftop -->
                        <rect x="15" y="20" width="70" height="8" fill="#10B981" rx="1"/>
                        <rect x="17" y="22" width="66" height="2" fill="white" opacity="0.3"/>
                        
                        <!-- Side buildings for depth -->
                        <rect x="10" y="35" width="15" height="60" fill="#4F46E5" opacity="0.7" rx="1"/>
                        <rect x="75" y="40" width="15" height="55" fill="#4F46E5" opacity="0.5" rx="1"/>
                    </svg>
                    <div>
                        <div class="company-name">ALTEZZA</div>
                    </div>
                </div>
                <div class="company-tagline">Property Management</div>
                <div class="company-address">
                    123 Property Lane, Suite 456<br>
                    Business District, City 12345<br>
                    Phone: (555) 123-4567<br>
                    Email: billing@altezzapm.com
                </div>
            </div>
            
            <div class="invoice-meta">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-number">#{{ $invoice->invoice_number }}</div>
                <div class="invoice-dates">
                    <div><strong>Issue Date:</strong> {{ $invoice->created_at->format('M d, Y') }}</div>
                    <div><strong>Due Date:</strong> {{ $invoice->due_date->format('M d, Y') }}</div>
                    @if($invoice->billing_period_start && $invoice->billing_period_end)
                        <div><strong>Period:</strong> {{ $invoice->billing_period_start->format('M d') }} - {{ $invoice->billing_period_end->format('M d, Y') }}</div>
                    @endif
                </div>
                <div style="margin-top: 10px;">
                    <span class="status-badge status-{{ $invoice->status }}">
                        {{ ucfirst($invoice->status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Bill To Section -->
        <div class="billing-section">
            <div class="bill-to">
                <div class="section-title">Bill To</div>
                <div class="owner-info">
                    <div class="owner-name">{{ $invoice->owner->name }}</div>
                    <div class="owner-details">
                        {{ $invoice->owner->email }}<br>
                        @if($invoice->apartment)
                            Apartment {{ $invoice->apartment->number }}@if($invoice->apartment->assessment_no), Assessment No {{ $invoice->apartment->assessment_no }}@endif
                        @endif
                    </div>
                </div>
            </div>
            
            @if($invoice->apartment)
            <div class="property-info">
                <div class="section-title">Property Details</div>
                <div class="property-details">
                    <strong>Apartment:</strong> {{ $invoice->apartment->number }}<br>
                    <strong>Type:</strong> {{ ucfirst($invoice->apartment->type ?? 'N/A') }}<br>
                    <strong>Area:</strong> {{ $invoice->apartment->area ?? 'N/A' }} sq ft<br>
                </div>
            </div>
            @endif
        </div>

        <!-- Invoice Items -->
        <div class="invoice-items">
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 50%;">Description</th>
                        <th style="width: 15%;">Period</th>
                        <th style="width: 10%;">Qty</th>
                        <th style="width: 12%;">Rate</th>
                        <th style="width: 13%;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @if($invoice->line_items && is_array($invoice->line_items))
                        @foreach($invoice->line_items as $item)
                        <tr>
                            <td>
                                <strong>{{ $item['description'] ?? 'Service' }}</strong>
                                @if(isset($item['details']))
                                    <br><small style="color: #6b7280;">{{ $item['details'] }}</small>
                                @endif
                            </td>
                            <td>{{ $item['period'] ?? '-' }}</td>
                            <td>{{ $item['quantity'] ?? 1 }}</td>
                            <td class="amount-cell">LKR {{ number_format($item['rate'] ?? 0, 2) }}</td>
                            <td class="amount-cell">LKR {{ number_format($item['amount'] ?? 0, 2) }}</td>
                        </tr>
                        @endforeach
                    @else
                        <!-- Default item based on invoice type -->
                        <tr>
                            <td>
                                <strong>
                                    @if($invoice->type === 'rent')
                                        Monthly Rent - Apartment {{ $invoice->apartment->number ?? 'N/A' }}
                                    @elseif($invoice->type === 'utility')
                                        Utility Charges
                                    @elseif($invoice->type === 'rooftop_reservation')
                                        Rooftop Reservation Fee
                                    @else
                                        {{ ucfirst($invoice->type) }} Charges
                                    @endif
                                </strong>
                                @if($invoice->description)
                                    <br><small style="color: #6b7280;">{{ $invoice->description }}</small>
                                @endif
                            </td>
                            <td>
                                @if($invoice->billing_period_start && $invoice->billing_period_end)
                                    {{ $invoice->billing_period_start->format('M d') }} - {{ $invoice->billing_period_end->format('M d') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>1</td>
                            <td class="amount-cell">LKR {{ number_format($invoice->amount, 2) }}</td>
                            <td class="amount-cell">LKR {{ number_format($invoice->amount, 2) }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Invoice Summary -->
        <div class="invoice-summary">
            <table class="summary-table">
                <tr>
                    <td class="label">Subtotal:</td>
                    <td class="value">LKR {{ number_format($invoice->amount, 2) }}</td>
                </tr>
                @if($invoice->discount > 0)
                <tr>
                    <td class="label">Discount:</td>
                    <td class="value">-LKR {{ number_format($invoice->discount, 2) }}</td>
                </tr>
                @endif
                @if($invoice->late_fee > 0)
                <tr>
                    <td class="label">Late Fee:</td>
                    <td class="value">LKR {{ number_format($invoice->late_fee, 2) }}</td>
                </tr>
                @endif
                <tr class="summary-total">
                    <td class="label">Total Amount:</td>
                    <td class="value">LKR {{ number_format($invoice->total_amount, 2) }}</td>
                </tr>
                @if($invoice->payments->sum('amount') > 0)
                <tr>
                    <td class="label">Amount Paid:</td>
                    <td class="value">LKR {{ number_format($invoice->payments->sum('amount'), 2) }}</td>
                </tr>
                <tr style="border-top: 2px solid #e5e7eb;">
                    <td class="label"><strong>Balance Due:</strong></td>
                    <td class="value"><strong>LKR {{ number_format($invoice->total_amount - $invoice->payments->sum('amount'), 2) }}</strong></td>
                </tr>
                @endif
            </table>
        </div>

        <!-- Modern Payment Terms -->
        <div class="payment-terms">
            <div class="terms-title">Payment Information</div>
            <div class="terms-content">
                <div class="mb-3">
                    <strong>Payment is due within 30 days of invoice date.</strong> Late payments may incur additional fees. 
                    Please include invoice number {{ $invoice->invoice_number }} with your payment.
                </div>
                
                <div class="payment-methods">
                    <strong class="mb-2" style="display: block; color: #059669;">Available Payment Methods:</strong>
                    <span class="payment-method">Online Portal: Login to your owner portal for secure online payment</span>
                    <span class="payment-method">Bank Transfer: Contact our billing department for transfer details</span>
                    <span class="payment-method">Check: Make payable to "Altezza Property Management"</span>
                </div>
                
                <div style="margin-top: 16px; padding-top: 12px; border-top: 1px solid #d1fae5; font-size: 9px; color: #6b7280;">
                    <strong>Questions?</strong> Contact our billing department at billing@altezzapm.com or (555) 123-4567
                </div>
            </div>
        </div>

        <!-- Modern Footer -->
        <div class="invoice-footer">
            <div class="contact-info">
                <span>🌐 www.altezzapm.com</span>
                <span>•</span>
                <span>📧 billing@altezzapm.com</span>
                <span>•</span>
                <span>📞 (555) 123-4567</span>
            </div>
            <div class="footer-tagline">
                Thank you for choosing Altezza Property Management • Invoice generated on {{ now()->format('M d, Y \a\t g:i A') }}
            </div>
        </div>
    </div>
</body>
</html>
