<?php

namespace Kobami\Khelpers\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class ImportDb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'k:import-db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'khelpers - Import db from parent site. Use config/khelpers.php';

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
      $parentServer = config('khelpers.import_server');
      $parentSitePath = config('khelpers.import_site_path');

      if (! ($parentServer && $parentSitePath)) {
        $this->error('missing config, see config/khelpers.php');
        return;
      }

      $this->info('parent server: ' . $parentServer);
      $this->info('parent site: ' . $parentSitePath);

      // dump new db file
      $this->info('dumping new sql file to tmp...');
      $dumpFile = $parentSitePath . '_' . date('Ymd-His', time());
      $cmd = "ssh $parentServer \"cd $parentSitePath && edbdump > tmp/$dumpFile\"";
      passthru($cmd);

      // get sql file list from remote tmp dir
      $cmd = "ssh $parentServer \"cd $parentSitePath && ls tmp\"";
      $output = shell_exec($cmd);

      // select file to import
      $files = array_reverse(explode("\n", trim($output)));
      $importFile = $this->choice('Import which file?', $files, 0);

      // download sql file
      $cmd = "scp $parentServer:$parentSitePath/tmp/$importFile tmp/.";
      $this->info($cmd);
      passthru($cmd);

      // import sql file
      $cmd = "edb < tmp/$importFile";
      $this->info($cmd);
      passthru($cmd);
    }
}
