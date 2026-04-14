<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Supplier;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupDuplicateSuppliers extends Command
{
    protected $signature = 'suppliers:cleanup-duplicates';

    protected $description = 'Remove duplicate suppliers based on phone number';

    public function handle(): int
    {
        $this->info('Finding duplicate suppliers...');

        // Find duplicate phone numbers
        $duplicatePhones = DB::table('suppliers')
            ->select('phone', DB::raw('COUNT(*) as count'))
            ->whereNull('deleted_at')
            ->groupBy('phone')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('phone');

        if ($duplicatePhones->isEmpty()) {
            $this->info('No duplicate suppliers found!');
            return self::SUCCESS;
        }

        $this->info("Found {$duplicatePhones->count()} phone number(s) with duplicates.");

        $totalDeleted = 0;

        foreach ($duplicatePhones as $phone) {
            $suppliers = Supplier::where('phone', $phone)->orderBy('id')->get();
            
            if ($suppliers->count() > 1) {
                $keep = $suppliers->first();
                $duplicates = $suppliers->skip(1);
                
                $this->line("Phone: {$phone}");
                $this->line("  Keeping: ID {$keep->id} - {$keep->name}");
                
                foreach ($duplicates as $duplicate) {
                    $this->line("  Deleting: ID {$duplicate->id} - {$duplicate->name}");
                    $duplicate->delete();
                    $totalDeleted++;
                }
            }
        }

        $this->info("Successfully deleted {$totalDeleted} duplicate supplier(s)!");

        return self::SUCCESS;
    }
}
