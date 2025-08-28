<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management Corporation Invoice - <?php echo e($invoice->invoice_number); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 2px solid #000;
        }
        
        .header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        
        .logo-section {
            width: 80px;
            margin-right: 20px;
        }
        
        .logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #4c1d95, #312e81);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
            border-radius: 8px;
        }
        
        .company-info {
            flex: 1;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .company-details {
            font-size: 10px;
            line-height: 1.3;
        }
        
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        
        .customer-info {
            flex: 1;
        }
        
        .invoice-details {
            text-align: right;
            width: 200px;
        }
        
        .invoice-title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 10px 0;
        }
        
        .outstanding-section {
            margin-bottom: 20px;
        }
        
        .outstanding-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        .outstanding-table th,
        .outstanding-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        
        .outstanding-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        
        .amount-col {
            text-align: right;
            width: 120px;
        }
        
        .charges-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .charges-table th,
        .charges-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }
        
        .charges-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 11px;
        }
        
        .charges-table .description-col {
            text-align: left;
            width: 200px;
        }
        
        .summary-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }
        
        .summary-table {
            width: 300px;
            border-collapse: collapse;
        }
        
        .summary-table th,
        .summary-table td {
            border: 1px solid #000;
            padding: 8px;
        }
        
        .summary-table th {
            background-color: #f5f5f5;
            text-align: center;
            font-weight: bold;
        }
        
        .summary-table td {
            text-align: right;
        }
        
        .total-amount {
            font-size: 16px;
            font-weight: bold;
            background-color: #e5e7eb;
        }
        
        .payment-instructions {
            margin-bottom: 20px;
        }
        
        .bank-details {
            background-color: #f9f9f9;
            padding: 15px;
            border: 1px solid #ccc;
            margin-bottom: 20px;
        }
        
        .bank-details h4 {
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .bank-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .bank-table td {
            padding: 5px 10px;
            border-bottom: 1px solid #ddd;
        }
        
        .bank-table td:first-child {
            font-weight: bold;
            width: 150px;
        }
        
        .terms-conditions {
            font-size: 10px;
            line-height: 1.3;
        }
        
        .terms-conditions h4 {
            font-size: 12px;
            margin-bottom: 10px;
        }
        
        .terms-conditions ol {
            margin-left: 20px;
        }
        
        .terms-conditions li {
            margin-bottom: 5px;
        }
        
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            font-size: 10px;
            font-style: italic;
        }
        
        .highlight {
            background-color: #fff3cd;
            padding: 2px 4px;
        }
        
        @media print {
            .invoice-container {
                border: none;
                padding: 0;
            }
            
            body {
                font-size: 11px;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header Section -->
        <div class="header">
            <div class="logo-section">
                <div class="logo">
                    A
                </div>
            </div>
            <div class="company-info">
                <div class="company-name">THE MANAGEMENT CORPORATION – ALTEZZA APARTMENT</div>
                <div class="company-details">
                    <div>(Condominium Plan No. <?php echo e($managementCorp->plan_number ?? '7538'); ?>, Registration No. <?php echo e($managementCorp->registration_number ?? 'CMA/CCU/2023/PVT/MC/1018'); ?>)</div>
                    <div><?php echo e($managementCorp->address ?? 'No. 202/1, AVERIWATTA ROAD, HUNUPITIYA, WATTALA'); ?></div>
                    <div>E-mail: <?php echo e($managementCorp->email ?? 'propertymanager.altezza@gmail.com'); ?></div>
                </div>
            </div>
        </div>

        <!-- Invoice Info Section -->
        <div class="invoice-info">
            <div class="customer-info">
                <table>
                    <tr>
                        <td><strong>Name of the Owner</strong></td>
                        <td><?php echo e($invoice->owner->name ?? 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Apartment No</strong></td>
                        <td><?php echo e($invoice->apartment->number); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Due Date</strong></td>
                        <td><?php echo e($invoice->due_date->format('d-M-Y')); ?></td>
                    </tr>
                </table>
            </div>
            <div class="invoice-details">
                <table>
                    <tr>
                        <td><strong>Invoice No</strong></td>
                        <td><?php echo e($invoice->invoice_number); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Invoice Date</strong></td>
                        <td><?php echo e($invoice->created_at->format('d-M-Y')); ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Invoice Title -->
        <div class="invoice-title">MC INVOICE</div>

        <!-- Outstanding Balance Section -->
        <div class="outstanding-section">
            <table class="outstanding-table">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Description</th>
                        <th class="amount-col">Amount (Rs.)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Previous Outstanding as of <?php echo e($previousPeriodEnd ?? '1st JANUARY ' . $invoice->year); ?></td>
                        <td class="amount-col"><?php echo e(number_format($previousOutstanding ?? 0, 2)); ?></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Total Payments as of <?php echo e($paymentsAsOf ?? '25th MARCH ' . $invoice->year); ?></td>
                        <td class="amount-col"><?php echo e(number_format($totalPayments ?? 0, 2)); ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><strong>Total Outstanding</strong></td>
                        <td class="amount-col"><strong><?php echo e(number_format($currentOutstanding ?? 0, 2)); ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Current Charges Section -->
        <table class="charges-table">
            <thead>
                <tr>
                    <th>S.No.</th>
                    <th class="description-col">Description</th>
                    <th>UOM</th>
                    <th>QTY</th>
                    <th>Unit Rate (Rs.)</th>
                    <th>No of Months / Days</th>
                    <th class="amount-col">Amount (Rs.)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>3</td>
                    <td class="description-col">Management Fees for <?php echo e($invoice->billing_period_start->format('M-y')); ?> to <?php echo e($invoice->billing_period_end->format('M-y')); ?></td>
                    <td>SQFT</td>
                    <td><?php echo e(number_format($invoice->area_sqft, 2)); ?></td>
                    <td><?php echo e(number_format($invoice->management_fee_ratio, 2)); ?></td>
                    <td>3</td>
                    <td class="amount-col"><?php echo e(number_format($invoice->quarterly_management_fee, 2)); ?></td>
                </tr>
                <tr>
                    <td>4</td>
                    <td class="description-col">Sinking Fund for <?php echo e($invoice->billing_period_start->format('M-y')); ?> to <?php echo e($invoice->billing_period_end->format('M-y')); ?></td>
                    <td>SQFT</td>
                    <td><?php echo e(number_format($invoice->area_sqft, 2)); ?></td>
                    <td><?php echo e(number_format($invoice->sinking_fund_ratio, 2)); ?></td>
                    <td>3</td>
                    <td class="amount-col"><?php echo e(number_format($invoice->quarterly_sinking_fund, 2)); ?></td>
                </tr>
                <?php if($invoice->late_fee > 0): ?>
                <tr>
                    <td>5</td>
                    <td class="description-col">Surcharge on Delayed Payment</td>
                    <td>%</td>
                    <td>2%</td>
                    <td><?php echo e(number_format($invoice->total_amount - $invoice->late_fee, 2)); ?></td>
                    <td>0.00</td>
                    <td class="amount-col"><?php echo e(number_format($invoice->late_fee, 2)); ?></td>
                </tr>
                <?php else: ?>
                <tr>
                    <td>5</td>
                    <td class="description-col">Surcharge on Delayed Payment</td>
                    <td>%</td>
                    <td>2%</td>
                    <td><?php echo e(number_format($invoice->total_amount, 2)); ?></td>
                    <td></td>
                    <td class="amount-col">0.00</td>
                </tr>
                <?php endif; ?>
                <tr style="border-top: 2px solid #000;">
                    <td colspan="6" style="text-align: right; font-weight: bold;">Total Charge for the Period</td>
                    <td class="amount-col" style="font-weight: bold;"><?php echo e(number_format($invoice->total_amount, 2)); ?></td>
                </tr>
            </tbody>
        </table>

        <!-- Invoice Summary -->
        <div class="summary-section">
            <table class="summary-table">
                <thead>
                    <tr>
                        <th colspan="2">Invoice Summary</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Current Dues</td>
                        <td><?php echo e(number_format($invoice->total_amount, 2)); ?></td>
                    </tr>
                    <tr>
                        <td>B/F</td>
                        <td><?php echo e(number_format($currentOutstanding ?? 0, 2)); ?></td>
                    </tr>
                    <tr class="total-amount">
                        <td><strong>Total Amount Payable</strong></td>
                        <td><strong><?php echo e(number_format(($invoice->total_amount + ($currentOutstanding ?? 0)), 2)); ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Payment Instructions -->
        <div class="payment-instructions">
            <p><strong>Please send your payment via</strong></p>
            <p>Through a Fund Transfer to the Bank Account indicated below with relevant Apartment number and name:</p>
        </div>

        <!-- Bank Details -->
        <div class="bank-details">
            <h4>Bank Account Details:</h4>
            <table class="bank-table">
                <tr>
                    <td>A/C Name</td>
                    <td><?php echo e($bankDetails['account_name'] ?? 'THE MCCP NO. 7538 ALTEZZA APARTMENT'); ?></td>
                </tr>
                <tr>
                    <td>A/C No</td>
                    <td><?php echo e($bankDetails['account_number'] ?? '035010047455'); ?></td>
                </tr>
                <tr>
                    <td>A/C Type</td>
                    <td><?php echo e($bankDetails['account_type'] ?? 'CURRENT ACCOUNT'); ?></td>
                </tr>
                <tr>
                    <td>Bank Name</td>
                    <td><?php echo e($bankDetails['bank_name'] ?? 'Hatton National Bank (7083)'); ?></td>
                </tr>
                <tr>
                    <td>Branch Name</td>
                    <td><?php echo e($bankDetails['branch_name'] ?? 'Wattala (035)'); ?></td>
                </tr>
                <tr>
                    <td>Swift Code</td>
                    <td><?php echo e($bankDetails['swift_code'] ?? 'HBLILKLX'); ?></td>
                </tr>
            </table>
        </div>

        <!-- Terms and Conditions -->
        <div class="terms-conditions">
            <h4>Special Notes:</h4>
            <ol>
                <li><strong>Invoice should be settled within Fourteen (14) days from the date of invoice and in case of non-settlement of the dues within the said 14 days period stipulated above:</strong></li>
                <li><strong>For apartment's with outstanding amounts for more than fourteen (14) days from the date of invoice released, a notice will be issued for facility disconnection.</strong></li>
                <li>If the payment is made after Fourteen (14) days from the invoice released, the Names of the owners will also be displayed through the Official WhatsApp group and the same list will be displayed on the notice boards.</li>
                <li><strong>For apartment's with due amount, after Thirty (30) days from the invoice released, Common Facilities such as Swimming pool, Gym and Parking will be disconnected & the Apartment's Water and LP Gas Supply will be disconnected.</strong></li>
                <li><strong>If the Apartment facility supply is required following the said disconnection, Facility reconnection fee of Rs. 4,000.00 will be charged for re-connection.</strong></li>
                <li><strong>For apartment's with outstanding amounts for more than fourteen (14) days from the date of invoice released, 2% Monthly Surcharge will be added.</strong></li>
                <li><strong>Any payment made after 25th MARCH <?php echo e($invoice->year); ?> might not be reflected in the outstanding given above.</strong></li>
                <li><strong>In the case of Direct Deposits / Fund Transfers/Standing orders, please state your Apartment No. in the beneficiary details and send the payment slip email to propertymanager.altezza@gmail.com . You can call Management Office on +94 117 108 634 for any inquiry.</strong></li>
                <li><strong>Those who have an outstanding balance as of 30 MARCH <?php echo e($invoice->year); ?>, please be kind enough to settle the same on or before 15th APRIL <?php echo e($invoice->year); ?> as per the CMA act, legal action will be taken.</strong></li>
            </ol>
        </div>

        <!-- Footer -->
        <div class="footer">
            This is a computer generated invoice and doesn't require a signature
        </div>
    </div>
</body>
</html>
<?php /**PATH F:\APPS\altezza\altezza\altezza\resources\views/management-fees/invoice-template.blade.php ENDPATH**/ ?>