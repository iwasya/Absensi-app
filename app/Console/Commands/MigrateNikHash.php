<?php

namespace App\Console\Commands;

use App\Models\UserSensitive;
use Illuminate\Console\Command;

class MigrateNikHash extends Command
{
    protected $signature = 'nik:migrate-hash';
    protected $description = 'Migrate existing nik_encrypted values to include nik_hash for fast lookup';

    public function handle(): int
    {
        $this->info('Migrating nik_hash for existing user_sensitive records...');

        $count = 0;
        $skipped = 0;

        UserSensitive::whereNotNull('nik_encrypted')
            ->whereNull('nik_hash')
            ->chunkById(100, function ($records) use (&$count, &$skipped) {
                foreach ($records as $record) {
                    try {
                        $decrypted = \Illuminate\Support\Facades\Crypt::decryptString($record->nik_encrypted);
                        $normalized = UserSensitive::normalizeNik($decrypted);
                        $record->nik_hash = hash('sha256', $normalized);
                        $record->save();
                        $count++;
                    } catch (\Exception $e) {
                        $skipped++;
                        $this->warn("Failed to decrypt NIK for id_sensitive: {$record->id_sensitive}");
                    }
                }
            });

        $this->info("Done! Migrated: {$count}, Skipped (decrypt failed): {$skipped}");

        if ($skipped > 0) {
            $this->warn('Some records were skipped due to decryption failures. Please check the data integrity.');
        }

        return Command::SUCCESS;
    }
}
