<?php

namespace App\Console\Commands;

use App\Models\Kindergarten;
use App\Services\InvoiceService;
use Illuminate\Console\Command;

class GenerateMonthlyInvoices extends Command
{
    protected $signature = 'invoice:generate {--month= : Billing month format YYYY-MM}';
    protected $description = 'Generate monthly recurring fee invoices for all active students';

    public function handle(InvoiceService $invoiceService): int
    {
        $month = $this->option('month') ?? now()->format('Y-m');
        $this->info("Generating recurring invoices for month: {$month}");

        $kindergartens = Kindergarten::all();
        $totalGenerated = 0;

        foreach ($kindergartens as $kindergarten) {
            $count = $invoiceService->generateMonthlyInvoices($kindergarten, $month . '-01');
            $this->line("  [{$kindergarten->name}]: {$count} invoices generated.");
            $totalGenerated += $count;
        }

        $this->info("Done! Total {$totalGenerated} invoices generated across {$kindergartens->count()} kindergartens.");
        return self::SUCCESS;
    }
}
