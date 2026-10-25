<?php

namespace App\Modules\Integration\Console\Commands;

use App\Modules\Integration\Models\IntegrationLog;
use Illuminate\Console\Command;

class PruneIntegrationLogsCommand extends Command
{
    protected $signature = 'integrations:prune-logs {--days= : Number of days to retain}';

    protected $description = 'Prune integration logs based on retention configuration';

    public function handle(): int
    {
        $days = $this->option('days') ?? config('integrations.logging.retention_days', 30);
        $cutoff = now()->subDays((int)$days);
        $count = IntegrationLog::query()->where('created_at', '<', $cutoff)->delete();
        $this->info("Pruned {$count} integration logs older than {$days} days.");
        return self::SUCCESS;
    }
}
