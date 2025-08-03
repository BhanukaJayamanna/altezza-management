<!DOCTYPE html>
<html>
<head>
    <title>Payment Voucher - {{ $voucher->voucher_number }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            line-height: 1.6;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #4F46E5;
            padding-bottom: 20px;
        }
        
        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #4F46E5;
            margin: 0;
        }
        
        .document-title {
            font-size: 22px;
            margin: 10px 0;
            color: #1F2937;
        }
        
        .voucher-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .voucher-details, .status-info {
            width: 48%;
        }
        
        .info-row {
            margin-bottom: 10px;
            display: flex;
        }
        
        .label {
            font-weight: bold;
            width: 120px;
            color: #374151;
        }
        
        .value {
            color: #111827;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-approved { background-color: #d1fae5; color: #065f46; }
        .status-rejected { background-color: #fee2e2; color: #991b1b; }
        .status-paid { background-color: #dbeafe; color: #1e40af; }
        
        .main-content {
            background-color: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .amount-section {
            text-align: center;
            background-color: #4F46E5;
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .amount-label {
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .amount-value {
            font-size: 32px;
            font-weight: bold;
        }
        
        .vendor-section, .description-section {
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #374151;
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        
        .description-text {
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #4F46E5;
        }
        
        .approval-section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
        }
        
        .signature-area {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
        }
        
        .signature-box {
            width: 200px;
            text-align: center;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            margin-bottom: 5px;
            height: 40px;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        table td {
            padding: 8px 0;
            vertical-align: top;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="company-name">{{ Setting::getValue('app_name', 'Altezza Property Management') }}</h1>
        <h2 class="document-title">PAYMENT VOUCHER</h2>
    </div>

    <div class="voucher-info">
        <div class="voucher-details">
            <div class="info-row">
                <span class="label">Voucher No:</span>
                <span class="value">{{ $voucher->voucher_number }}</span>
            </div>
            <div class="info-row">
                <span class="label">Date:</span>
                <span class="value">{{ $voucher->voucher_date->format('d/m/Y') }}</span>
            </div>
            <div class="info-row">
                <span class="label">Payment Method:</span>
                <span class="value">{{ $voucher->payment_method_display }}</span>
            </div>
            @if($voucher->reference_number)
            <div class="info-row">
                <span class="label">Reference:</span>
                <span class="value">{{ $voucher->reference_number }}</span>
            </div>
            @endif
            @if($voucher->apartment)
            <div class="info-row">
                <span class="label">Unit:</span>
                <span class="value">Apartment {{ $voucher->apartment->number }}</span>
            </div>
            @endif
        </div>
        
        <div class="status-info">
            <div class="info-row">
                <span class="label">Status:</span>
                <span class="status-badge status-{{ $voucher->status }}">
                    {{ ucfirst($voucher->status) }}
                </span>
            </div>
            <div class="info-row">
                <span class="label">Category:</span>
                <span class="value">{{ ucfirst($voucher->expense_category) }}</span>
            </div>
            <div class="info-row">
                <span class="label">Created By:</span>
                <span class="value">{{ $voucher->creator->name }}</span>
            </div>
            @if($voucher->approver)
            <div class="info-row">
                <span class="label">Approved By:</span>
                <span class="value">{{ $voucher->approver->name }}</span>
            </div>
            <div class="info-row">
                <span class="label">Approved On:</span>
                <span class="value">{{ $voucher->approved_at->format('d/m/Y H:i') }}</span>
            </div>
            @endif
        </div>
    </div>

    <div class="amount-section">
        <div class="amount-label">AMOUNT TO BE PAID</div>
        <div class="amount-value">₹{{ number_format($voucher->amount, 2) }}</div>
    </div>

    <div class="main-content">
        <div class="vendor-section">
            <div class="section-title">VENDOR DETAILS</div>
            <table>
                <tr>
                    <td style="width: 120px;"><strong>Name:</strong></td>
                    <td>{{ $voucher->vendor_name }}</td>
                </tr>
                @if($voucher->vendor_phone)
                <tr>
                    <td><strong>Phone:</strong></td>
                    <td>{{ $voucher->vendor_phone }}</td>
                </tr>
                @endif
                @if($voucher->vendor_email)
                <tr>
                    <td><strong>Email:</strong></td>
                    <td>{{ $voucher->vendor_email }}</td>
                </tr>
                @endif
                @if($voucher->vendor_address)
                <tr>
                    <td><strong>Address:</strong></td>
                    <td>{{ $voucher->vendor_address }}</td>
                </tr>
                @endif
            </table>
        </div>

        <div class="description-section">
            <div class="section-title">DESCRIPTION / PURPOSE</div>
            <div class="description-text">
                {{ $voucher->description }}
            </div>
        </div>

        @if($voucher->approval_notes)
        <div class="description-section">
            <div class="section-title">APPROVAL NOTES</div>
            <div class="description-text">
                {{ $voucher->approval_notes }}
            </div>
        </div>
        @endif
    </div>

    <div class="approval-section">
        <div class="signature-area">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div><strong>Prepared By</strong></div>
                <div>{{ $voucher->creator->name }}</div>
                <div>{{ $voucher->created_at->format('d/m/Y') }}</div>
            </div>
            
            <div class="signature-box">
                <div class="signature-line"></div>
                <div><strong>Approved By</strong></div>
                @if($voucher->approver)
                <div>{{ $voucher->approver->name }}</div>
                <div>{{ $voucher->approved_at->format('d/m/Y') }}</div>
                @else
                <div>_________________</div>
                <div>Date: ___________</div>
                @endif
            </div>
            
            <div class="signature-box">
                <div class="signature-line"></div>
                <div><strong>Received By</strong></div>
                <div>_________________</div>
                <div>Date: ___________</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>This is a system-generated voucher from {{ Setting::getValue('app_name', 'Altezza Property Management') }}</p>
        <p>Generated on {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
