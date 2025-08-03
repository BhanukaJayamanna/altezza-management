<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UtilityBill;
use App\Services\UtilityBillService;
use App\Models\Invoice;

class GenerateUtilityInvoices extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'utility:generate-invoices {--bill-id=* : Specific utility bill IDs to generate invoices for} {--month= : Month to generate invoices for} {--year= : Year to generate invoices for}';

    /**
     * The console command description.
     */
    protected $description = 'Generate invoices for utility bills that don\'t have associated invoices';

    protected $utilityBillService;

    public function __construct(UtilityBillService $utilityBillService)
    {
        parent::__construct();
        $this->utilityBillService = $utilityBillService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $billIds = $this->option('bill-id');
        $month = $this->option('month');
        $year = $this->option('year');

        if (!empty($billIds)) {
            // Generate invoices for specific bills
            $this->generateForSpecificBills($billIds);
        } elseif ($month && $year) {
            // Generate invoices for specific month/year
            $this->generateForPeriod($month, $year);
        } else {
            // Generate invoices for all utility bills without invoices
            $this->generateForAllBills();
        }
    }

    private function generateForSpecificBills(array $billIds)
    {
        $bills = UtilityBill::whereIn('id', $billIds)
            ->whereNull('invoice_id')
            ->with(['apartment.currentLease', 'tenant'])
            ->get();

        $this->info("Found {$bills->count()} utility bills to generate invoices for.");

        $generated = 0;
        foreach ($bills as $bill) {
            try {
                $invoice = $this->utilityBillService->createInvoiceFromUtilityBill($bill);
                $bill->update(['invoice_id' => $invoice->id]);
                $generated++;
                $this->info("Generated invoice {$invoice->invoice_number} for utility bill {$bill->id}");
            } catch (\Exception $e) {
                $this->error("Failed to generate invoice for utility bill {$bill->id}: " . $e->getMessage());
            }
        }

        $this->info("Successfully generated {$generated} invoices.");
    }

    private function generateForPeriod($month, $year)
    {
        $bills = UtilityBill::where('month', $month)
            ->where('year', $year)
            ->whereNull('invoice_id')
            ->with(['apartment.currentLease', 'tenant'])
            ->get();

        $this->info("Found {$bills->count()} utility bills for {$month}/{$year} to generate invoices for.");

        $generated = 0;
        foreach ($bills as $bill) {
            try {
                $invoice = $this->utilityBillService->createInvoiceFromUtilityBill($bill);
                $bill->update(['invoice_id' => $invoice->id]);
                $generated++;
                $this->info("Generated invoice {$invoice->invoice_number} for utility bill {$bill->id}");
            } catch (\Exception $e) {
                $this->error("Failed to generate invoice for utility bill {$bill->id}: " . $e->getMessage());
            }
        }

        $this->info("Successfully generated {$generated} invoices for {$month}/{$year}.");
    }

    private function generateForAllBills()
    {
        $bills = UtilityBill::whereNull('invoice_id')
            ->with(['apartment.currentLease', 'tenant'])
            ->get();

        $this->info("Found {$bills->count()} utility bills without invoices.");

        $generated = 0;
        foreach ($bills as $bill) {
            try {
                $invoice = $this->utilityBillService->createInvoiceFromUtilityBill($bill);
                $bill->update(['invoice_id' => $invoice->id]);
                $generated++;
                $this->info("Generated invoice {$invoice->invoice_number} for utility bill {$bill->id}");
            } catch (\Exception $e) {
                $this->error("Failed to generate invoice for utility bill {$bill->id}: " . $e->getMessage());
            }
        }

        $this->info("Successfully generated {$generated} invoices from existing utility bills.");
    }
}
