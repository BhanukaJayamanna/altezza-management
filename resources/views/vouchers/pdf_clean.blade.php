<!DOCTYPE html>
<html>
<head>
    <title>Payment Voucher - {{ $voucher->voucher_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 15px;
            line-height: 1.2;
            color: #000;
            font-size: 12px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #000;
            padding: 0;
        }
        
        .header {
            background: #4a5a9c;
            color: white;
            padding: 10px;
            text-align: center;
            position: relative;
        }
        
        .logo {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            width: 50px;
            height: 50px;
            background: white;
            color: #4a5a9c;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 24px;
        }
        
        .company-info {
            margin: 0;
            padding: 0;
        }
        
        .company-name {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        
        .company-details {
            font-size: 10px;
            margin: 2px 0;
        }
        
        .voucher-title {
            background: #000;
            color: white;
            text-align: center;
            padding: 8px;
            font-size: 14px;
            font-weight: bold;
            margin: 0;
        }
        
        .voucher-header {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
        }
        
        .voucher-header tr {
            display: table-row;
        }
        
        .voucher-header-cell {
            border: 1px solid #000;
            padding: 5px;
            font-weight: bold;
            background: #000;
            color: white;
            text-align: center;
            width: 25%;
        }
        
        .voucher-data-cell {
            border: 1px solid #000;
            padding: 5px;
            width: 25%;
        }
        
        .section {
            margin: 0;
            padding: 10px;
        }
        
        .payee-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        
        .payee-table td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: top;
        }
        
        .payee-header {
            background: #000;
            color: white;
            font-weight: bold;
            text-align: center;
            width: 15%;
        }
        
        .payment-method {
            margin: 10px 0;
        }
        
        .payment-method table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .payment-method td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            font-weight: bold;
        }
        
        .payment-method .header {
            background: #000;
            color: white;
            width: 15%;
        }
        
        .checkbox {
            width: 15px;
            height: 15px;
            border: 1px solid #000;
            display: inline-block;
            text-align: center;
            line-height: 13px;
        }
        
        .signature-section {
            margin-top: 20px;
        }
        
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .signature-table td {
            border: 1px solid #000;
            padding: 30px 10px 10px 10px;
            text-align: center;
            width: 50%;
            height: 60px;
            vertical-align: bottom;
        }
        
        .amount-words {
            margin: 10px 0;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">A</div>
            <div class="company-info">
                <div class="company-name">THE MANAGEMENT CORPORATION – ALTEZZA APARTMENT</div>
                <div class="company-details">(Condominium Plan No. 7538, Registration No. CMA/CCU/2023/PVT/MC/1018)</div>
                <div class="company-details">No. 202/1, AVERIWATTA ROAD, WATTALA  Tel: 011-7108831  E-mail: propertymgr.altezza@gmail.com</div>
            </div>
        </div>
        
        <!-- Voucher Title -->
        <div class="voucher-title">PAYMENT VOUCHER</div>
        
        <!-- Voucher Header Info -->
        <table class="voucher-header">
            <tr>
                <td class="voucher-header-cell">VOUCHER NO</td>
                <td class="voucher-data-cell">{{ $voucher->voucher_number }}</td>
                <td class="voucher-header-cell">DATE</td>
                <td class="voucher-data-cell">{{ $voucher->voucher_date->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="voucher-header-cell">CHEQUE NO</td>
                <td class="voucher-data-cell">{{ $voucher->reference_number ?? '' }}</td>
                <td class="voucher-header-cell">AMOUNT (in LKR)</td>
                <td class="voucher-data-cell">{{ number_format($voucher->amount, 2) }}</td>
            </tr>
        </table>
        
        <!-- Amount in Words -->
        <div class="section">
            <div class="amount-words">
                This supplies/Services/Works were duly authorized and performed and that the payment of Rupees<br>
                <strong>{{ strtoupper(number_to_words($voucher->amount)) }} ONLY</strong><br>
                is in accordance with fair, reasonable regulations.
            </div>
        </div>
        
        <!-- Payee Details -->
        <table class="payee-table">
            <tr>
                <td class="payee-header">DETAILS OF PAYEE</td>
                <td style="width: 15%; font-weight: bold;">NAME</td>
                <td style="width: 70%;">{{ strtoupper($voucher->vendor_name) }}</td>
            </tr>
            <tr>
                <td class="payee-header" style="vertical-align: middle; height: 40px;"></td>
                <td style="font-weight: bold;">ADDRESS</td>
                <td>{{ strtoupper($voucher->vendor_address) }}</td>
            </tr>
            <tr>
                <td></td>
                <td style="font-weight: bold;">NIC / BR</td>
                <td>{{ $voucher->vendor_nic ?? '' }}</td>
                <td style="font-weight: bold;">CONTACT NO</td>
                <td>{{ $voucher->vendor_phone }}</td>
            </tr>
        </table>
        
        <!-- Purpose -->
        <table class="payee-table">
            <tr>
                <td class="payee-header">PURPOSE OF PAYMENT</td>
                <td style="width: 85%;">{{ strtoupper($voucher->description) }}</td>
            </tr>
        </table>
        
        <!-- Payment Method -->
        <div class="payment-method">
            <table>
                <tr>
                    <td class="header">PAYMENT METHOD</td>
                    <td style="width: 20%;">
                        BANK DEPOSIT<br>
                        <span class="checkbox">{{ $voucher->payment_method === 'bank_transfer' ? '✓' : '' }}</span>
                    </td>
                    <td style="width: 20%;">
                        DIRECT PAY<br>
                        <span class="checkbox">{{ $voucher->payment_method === 'cash' ? '✓' : '' }}</span>
                    </td>
                    <td style="width: 22%;">
                        Received By<br>
                        <br>
                    </td>
                    <td style="width: 23%;">
                        Prepared By<br>
                        <br>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- Signatures -->
        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td>
                        <strong>Approved By (I)</strong><br>
                        <strong>SIGNATURE</strong>
                    </td>
                    <td>
                        <strong>Approved By (II)</strong><br>
                        <strong>SIGNATURE</strong>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
