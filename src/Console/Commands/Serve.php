<?php

namespace Kobami\Khelpers\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class Serve extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'k:serve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'khelpers - Serve site at localhost:8000';

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
        $url = env('APP_URL');
        $host = parse_url($url, PHP_URL_HOST);
        $port = parse_url($url, PHP_URL_PORT);

        $this->call('serve', ['--host' => $host, '--port' => $port]);
    }
}
