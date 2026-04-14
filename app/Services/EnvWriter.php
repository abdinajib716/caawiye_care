<?php

declare(strict_types=1);

namespace App\Services;

use App\Concerns\HasActionLogTrait;
use App\Enums\ActionType;
use App\Enums\Hooks\CommonFilterHook;
use App\Support\Facades\Hook;

class EnvWriter
{
    use HasActionLogTrait;

    public function write($key, $value): void
    {
        // If the value didn't change, don't write it to the file.
        if ($this->get($key) === $value) {
            return;
        }

        $path = base_path('.env');
        $file = file_get_contents($path);

        // Wrap the value in double quotes.
        $formattedValue = "\"$value\"";

        $file = preg_replace("/^$key=.*/m", "$key=$formattedValue", $file);

        // If the key doesn't exist, append it
        if (! preg_match("/^$key=/m", $file)) {
            $file .= PHP_EOL."$key=$formattedValue";
        }

        // Use file locking to prevent race conditions
        $fp = fopen($path, 'c+');
        if (flock($fp, LOCK_EX)) {
            ftruncate($fp, 0);
            fwrite($fp, $file);
            fflush($fp);
            flock($fp, LOCK_UN);
        }
        fclose($fp);
    }

    public function get($key)
    {
        $path = base_path('.env');
        $file = file_get_contents($path);
        preg_match("/^$key=(.*)/m", $file, $matches);

        return isset($matches[1]) ? trim($matches[1]) : null;
    }

    public function maybeWriteKeysToEnvFile($keys): void
    {
        $availableKeys = $this->getAvailableKeys();

        // Stop if no keys are matching to availableKeys.
        if (empty($keys) || empty($availableKeys)) {
            return;
        }

        foreach ($keys as $key => $value) {
            if (array_key_exists($key, $availableKeys)) {
                $this->write($availableKeys[$key], (string) $value);
            }
        }
    }

    public function getAvailableKeys()
    {
        return Hook::applyFilters(CommonFilterHook::AVAILABLE_KEYS, [
            'app_name' => 'APP_NAME',
        ]);
    }

    public function batchWriteKeysToEnvFile(array $keys): void
    {
        try {
            $availableKeys = $this->getAvailableKeys();

            if (empty($keys) || empty($availableKeys)) {
                return;
            }

            $path = base_path('.env');
            $file = file_get_contents($path);

            $changesMade = false;

            foreach ($keys as $key => $value) {
                if (array_key_exists($key, $availableKeys)) {
                    $envKey = $availableKeys[$key];
                    $currentValue = $this->get($envKey);

                    // Normalize both values for proper comparison
                    $normalizedCurrentValue = trim(trim($currentValue, '"'), "'");
                    $normalizedNewValue = trim((string) $value);

                    // Skip if value is null or empty string submitted (but allow '0' and other falsy values)
                    if ($value === null || ($value === '' && $key !== 'mail_encryption')) {
                        continue;
                    }

                    // Skip writing if the normalized values are identical
                    if ($normalizedCurrentValue === $normalizedNewValue) {
                        continue;
                    }

                    // Format the value with quotes (escape existing quotes)
                    $formattedValue = '"' . str_replace('"', '\\"', $normalizedNewValue) . '"';
                    
                    // Update or add the key
                    if (preg_match("/^$envKey=/m", $file)) {
                        $file = preg_replace("/^$envKey=.*/m", "$envKey=$formattedValue", $file);
                    } else {
                        $file .= PHP_EOL . "$envKey=$formattedValue";
                    }

                    $changesMade = true;
                }
            }

            // Write to the file only if changes were made
            if ($changesMade) {
                // Use file_put_contents with LOCK_EX for atomic write
                $bytesWritten = file_put_contents($path, $file, LOCK_EX);
                
                if ($bytesWritten === false) {
                    \Log::error('EnvWriter: Failed to write to .env file', [
                        'error' => error_get_last(),
                        'path' => $path,
                        'is_writable' => is_writable($path)
                    ]);
                }
                
                // Force PHP to reload the .env file
                if (function_exists('opcache_reset')) {
                    opcache_reset();
                }
                
                // Clear file status cache
                clearstatcache(true, $path);
            }
        } catch (\Throwable $th) {
            $this->storeActionLog(ActionType::EXCEPTION, [
                'env_update_error' => $th->getMessage(),
            ]);
        }
    }
}
