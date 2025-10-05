<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Spatie\Permission\PermissionRegistrar;

class ClearPermissionCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:clear-cache
                            {--force : Force clear cache files even if permission denied}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear the permission cache completely (fixes permission cache issues)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Clearing permission cache...');

        // Step 1: Use Spatie's built-in cache clear
        try {
            app(PermissionRegistrar::class)->forgetCachedPermissions();
            $this->info('✓ Spatie permission cache cleared');
        } catch (\Exception $e) {
            $this->warn('⚠ Spatie cache clear failed: ' . $e->getMessage());
        }

        // Step 2: Clear Laravel cache
        try {
            cache()->forget(config('permission.cache.key', 'spatie.permission.cache'));
            $this->info('✓ Laravel cache cleared');
        } catch (\Exception $e) {
            $this->warn('⚠ Laravel cache clear failed: ' . $e->getMessage());
        }

        // Step 3: Clear all cache
        try {
            Artisan::call('cache:clear');
            $this->info('✓ Application cache cleared');
        } catch (\Exception $e) {
            $this->warn('⚠ Application cache clear failed: ' . $e->getMessage());
        }

        // Step 4: Manually delete cache files if force option is used
        if ($this->option('force')) {
            $this->info('Force clearing cache files...');
            
            $cacheDir = storage_path('framework/cache/data');
            
            if (File::exists($cacheDir)) {
                try {
                    // Try to delete cache files
                    $deleted = File::deleteDirectory($cacheDir, true);
                    
                    if ($deleted) {
                        // Recreate the directory
                        File::makeDirectory($cacheDir, 0775, true);
                        $this->info('✓ Cache files forcefully deleted');
                    } else {
                        $this->warn('⚠ Could not delete cache files. You may need to run:');
                        $this->warn('   sudo rm -rf storage/framework/cache/data/*');
                    }
                } catch (\Exception $e) {
                    $this->error('✗ Failed to delete cache files: ' . $e->getMessage());
                    $this->warn('Run this command with sudo or execute:');
                    $this->warn('   sudo rm -rf storage/framework/cache/data/*');
                }
            }
        }

        // Step 5: Verify cache is cleared
        $this->info('');
        $this->info('Verifying cache status...');
        
        $cacheExists = cache()->has(config('permission.cache.key', 'spatie.permission.cache'));
        
        if ($cacheExists) {
            $this->error('✗ Cache still exists! Permission cache was not fully cleared.');
            $this->warn('');
            $this->warn('This usually happens due to file permission issues.');
            $this->warn('Please run one of the following commands:');
            $this->warn('');
            $this->warn('  1. sudo php artisan permission:clear-cache --force');
            $this->warn('  2. sudo rm -rf storage/framework/cache/data/*');
            $this->warn('');
            return self::FAILURE;
        }

        $this->info('✓ Cache successfully cleared!');
        $this->info('');
        
        // Step 6: Reload permissions to rebuild cache
        $this->info('Rebuilding permission cache...');
        $registrar = app(PermissionRegistrar::class);
        $permissions = $registrar->getPermissions();
        
        $this->info('✓ Permission cache rebuilt with ' . $permissions->count() . ' permissions');
        $this->info('');
        $this->info('✅ Permission cache cleared and rebuilt successfully!');
        
        return self::SUCCESS;
    }
}

