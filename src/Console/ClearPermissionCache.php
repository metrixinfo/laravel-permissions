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
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $user_id = $this->option('user_id');
        if (!$user_id) {
            if ($this->confirm('This will delete the cached permission for ALL users. Are you sure?')) {
                Cache::tags(['acl'])->flush();
                $this->info('All permissions have been flushed from the cache.');
            }
        } else {
            Cache::tags(['acl:' . $user_id])->flush();
            $this->info('Permissions for user ' . $user_id . ' have been flushed from the cache.');
        }
    }
}
