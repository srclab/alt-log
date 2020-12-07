<?php

namespace SrcLab\AltLog\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alt-log:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Alt log';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->comment('Publishing assets...');
        $this->callSilent('vendor:publish', [
            '--tag' => 'alt-log-assets',
        ]);

        $this->comment('Publishing configuration...');
        $this->callSilent('vendor:publish', [
            '--tag' => 'alt-log-config',
        ]);

        $this->info('Alt log installed successfully.');
    }
}
