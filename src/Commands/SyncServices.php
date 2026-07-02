<?php namespace Jake142\Service\Commands;

use Illuminate\Console\Command;
use Jake142\Service\Composer;

class SyncServices extends Command
{
    protected $signature = 'laravel-service:sync';

    protected $description = 'Normalize laravel-service constraints and re-resolve path packages (run after switching git branches)';

    public function handle(Composer $composer)
    {
        try {
            $this->info('Syncing laravel-service packages...');
            $composer->syncLaravelServices();
            $this->info('laravel-service packages synced.');
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
