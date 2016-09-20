<?php

namespace ZiNETHQ\Shinobi\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shinobi:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the ZiNETHQ Shinobi migrations into the application';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->confirm('Installing Shinobi migrations, do you wish to continue? [y|N]')) {
            foreach ($this->getMigrations() as $key => $migration) {

                $timestamp = date('Y_m_d_His', time() + $key);

                copy(
                    realpath(__DIR__."/../../migrations/{$migration}.php"),
                    database_path("migrations/{$timestamp}_{$migration}.php")
                );
            }
        }

        $this->comment('ZiNETHQ Shinobi installed. Inspirational phrase!');
    }

    /**
     * Get the appropriate migration files.
     *
     * @return array
     */
    protected function getMigrations()
    {
        return [
            'create_roles_table',
            'create_permissions_table',
            'create_permission_role_table',
            'create_role_team_table',
        ];
    }
}
