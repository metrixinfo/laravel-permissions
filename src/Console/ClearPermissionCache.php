<?php

namespace Metrix\LaravelPermissions\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

/**
 *  Clear permission cache console command
 */
class ClearPermissionCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'acl:clear {--u|user_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all cached user and role permissions from the cache.';

    /**
     * @var bool
     */
    private $cache_tagging = true;

    public function __construct()
    {
        parent::__construct();

        $this->cache_tagging = \config('permissions.cache_tagging', true);
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $user_id = $this->option('user_id');
        if (!$user_id) {
            $result = $this->flushAllPermissions();
        } else {
            $result = $this->flushUserPermissions($user_id);
        }

        if ($result) {
            $this->info('Permissions have been flushed from the cache.');
        } else {
            $this->info('Failed to flush permissions.');
        }
    }

    /**
     * Flush ALL permissions from the cache.
     */
    private function flushAllPermissions(): bool
    {
        if (!$this->confirm('This will delete the cached permission for ALL users. Are you sure?')) {
            return false;
        }

        if ($this->cache_tagging) {
            return Cache::tags(['acl'])->flush();
        } else {
            $this->warn('Only caches with tagging can be cleared.');
        }
    }

    /**
     *
     */
    private function flushUserPermissions($user_id): bool
    {
        if ($this->cache_tagging) {
            Cache::tags(['acl:' . $user_id])->flush();
        } else {
            $this->warn('Only caches with tagging can be cleared.');
        }
    }
}
