<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rooftop Reservation - {{ $rooftopReservation->reservation_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #4f46e5;
            margin-bottom: 30px;
            padding-bottom: 20px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #4f46e5;
            margin-bottom: 5px;
        }
        
        .document-title {
            font-size: 20px;
            margin-top: 15px;
            color: #374151;
        }
        
        .reservation-info {
            background: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .info-section {
            background: white;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 5px 0;
        }
        
        .detail-label {
            font-weight: bold;
            color: #6b7280;
        }
        
        .detail-value {
            color: #374151;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-confirmed { background: #d1fae5; color: #047857; }
        .status-cancelled { background: #fee2e2; color: #dc2626; }
        .status-completed { background: #dbeafe; color: #1d4ed8; }
        
        .pricing-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .pricing-table th,
        .pricing-table td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .pricing-table th {
            background: #f9fafb;
            font-weight: bold;
            color: #374151;
        }
        
        .total-row {
            font-weight: bold;
            background: #f3f4f6;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
        }
        
        .terms-section {
            margin-top: 30px;
            padding: 20px;
            background: #f9fafb;
            border-radius: 8px;
        }
        
        .equipment-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .equipment-item {
            background: #e0e7ff;
            color: #3730a3;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">Altezza Property Management</div>
        <div class="document-title">Rooftop Reservation Agreement</div>
    </div>

    <div class="reservation-info">
        <div class="detail-row">
            <span class="detail-label">Reservation Number:</span>
            <span class="detail-value">{{ $rooftopReservation->reservation_number }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Status:</span>
            <span class="status-badge status-{{ $rooftopReservation->status }}">{{ $rooftopReservation->status_display }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Created Date:</span>
            <span class="detail-value">{{ $rooftopReservation->created_at->format('F j, Y g:i A') }}</span>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-section">
            <div class="section-title">Owner Information</div>
            <div class="detail-row">
                <span class="detail-label">Name:</span>
                <span class="detail-value">{{ $rooftopReservation->owner->name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Email:</span>
                <span class="detail-value">{{ $rooftopReservation->owner->email }}</span>
            </div>
            @if($rooftopReservation->owner->phone)
            <div class="detail-row">
                <span class="detail-label">Phone:</span>
                <span class="detail-value">{{ $rooftopReservation->owner->phone }}</span>
            </div>
            @endif
            <div class="detail-row">
                <span class="detail-label">Apartment:</span>
                <span class="detail-value">{{ $rooftopReservation->apartment->number }}
                    @if($rooftopReservation->apartment->assessment_no) - Assessment No {{ $rooftopReservation->apartment->assessment_no }}@endif
                </span>
            </div>
        </div>

        <div class="info-section">
            <div class="section-title">Event Details</div>
            <div class="detail-row">
                <span class="detail-label">Event Title:</span>
                <span class="detail-value">{{ $rooftopReservation->event_title }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Event Type:</span>
                <span class="detail-value">{{ $rooftopReservation->event_type_display }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Expected Guests:</span>
                <span class="detail-value">{{ $rooftopReservation->expected_guests }} people</span>
            </div>
            @if($rooftopReservation->event_description)
            <div class="detail-row">
                <span class="detail-label">Description:</span>
                <span class="detail-value">{{ $rooftopReservation->event_description }}</span>
            </div>
            @endif
        </div>
    </div>

    <div class="info-grid">
        <div class="info-section">
            <div class="section-title">Reservation Schedule</div>
            <div class="detail-row">
                <span class="detail-label">Date:</span>
                <span class="detail-value">{{ $rooftopReservation->reservation_date->format('l, F j, Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Time:</span>
                <span class="detail-value">{{ $rooftopReservation->formatted_time_slot }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Duration:</span>
                <span class="detail-value">{{ $rooftopReservation->duration_hours }} hour(s)</span>
            </div>
        </div>

        <div class="info-section">
            <div class="section-title">Payment Schedule</div>
            @if($rooftopReservation->deposit_due_date)
            <div class="detail-row">
                <span class="detail-label">Deposit Due:</span>
                <span class="detail-value">{{ $rooftopReservation->deposit_due_date->format('F j, Y') }}</span>
            </div>
            @endif
            @if($rooftopReservation->final_payment_due_date)
            <div class="detail-row">
                <span class="detail-label">Final Payment Due:</span>
                <span class="detail-value">{{ $rooftopReservation->final_payment_due_date->format('F j, Y') }}</span>
            </div>
            @endif
            <div class="detail-row">
                <span class="detail-label">Total Paid:</span>
                <span class="detail-value">{{ currency($rooftopReservation->total_paid) }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Remaining:</span>
                <span class="detail-value">{{ currency($rooftopReservation->remaining_amount) }}</span>
            </div>
        </div>
    </div>

    <!-- Pricing Breakdown -->
    <table class="pricing-table">
        <thead>
            <tr>
                <th>Description</th>
                <th style="text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Base Rate</td>
                <td style="text-align: right;">{{ currency($rooftopReservation->base_rate) }}</td>
            </tr>
            <tr>
                <td>Hourly Rate ({{ $rooftopReservation->duration_hours }} hours)</td>
                <td style="text-align: right;">{{ currency($rooftopReservation->hourly_rate * $rooftopReservation->duration_hours) }}</td>
            </tr>
            <tr>
                <td>Cleaning Fee</td>
                <td style="text-align: right;">{{ currency($rooftopReservation->cleaning_fee) }}</td>
            </tr>
            <tr>
                <td>Security Deposit</td>
                <td style="text-align: right;">{{ currency($rooftopReservation->security_deposit) }}</td>
            </tr>
            @if($rooftopReservation->additional_charges > 0)
            <tr>
                <td>Additional Charges</td>
                <td style="text-align: right;">{{ currency($rooftopReservation->additional_charges) }}</td>
            </tr>
            @endif
            @if($rooftopReservation->discount > 0)
            <tr>
                <td>Discount</td>
                <td style="text-align: right;">-{{ currency($rooftopReservation->discount) }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td>Total Amount</td>
                <td style="text-align: right;">{{ currency($rooftopReservation->total_amount) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Equipment Requested -->
    @if($rooftopReservation->equipment_requested && count($rooftopReservation->equipment_requested) > 0)
    <div class="info-section">
        <div class="section-title">Equipment Requested</div>
        <div class="equipment-list">
            @foreach($rooftopReservation->equipment_requested as $equipment)
                <span class="equipment-item">{{ ucfirst(str_replace('_', ' ', $equipment)) }}</span>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Special Requirements -->
    @if($rooftopReservation->special_requirements)
    <div class="info-section">
        <div class="section-title">Special Requirements</div>
        <p>{{ $rooftopReservation->special_requirements }}</p>
    </div>
    @endif

    <!-- Terms and Conditions -->
    @if($rooftopReservation->terms_conditions)
    <div class="terms-section">
        <div class="section-title">Terms and Conditions</div>
        <p>{{ $rooftopReservation->terms_conditions }}</p>
    </div>
    @endif

    @if($rooftopReservation->approved_by)
    <div class="info-section">
        <div class="section-title">Approval Information</div>
        <div class="detail-row">
            <span class="detail-label">Approved By:</span>
            <span class="detail-value">{{ $rooftopReservation->approvedBy->name }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Approved On:</span>
            <span class="detail-value">{{ $rooftopReservation->approved_at->format('F j, Y g:i A') }}</span>
        </div>
    </div>
    @endif

    <div class="footer">
        <p>This document was generated on {{ now()->format('F j, Y g:i A') }}</p>
        <p>Altezza Property Management System - Rooftop Reservation Agreement</p>
        @if($rooftopReservation->invoice)
        <p>Related Invoice: {{ $rooftopReservation->invoice->invoice_number }}</p>
        @endif
    </div>
</body>
</html>
