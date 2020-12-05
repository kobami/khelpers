<?php

namespace Kobami\Khelpers\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class DownloadWordPress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'k:download-wordpress';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'khelpers - Download WordPress to /public. NOTE: existing Laravel files will be removed first.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Downloading WordPress.');
        $cmd = "rm -r public && mkdir public && cd public && ../vendor/bin/wp core download";
        shell_exec($cmd);
        $this->info('Done!');
    }
}
