<?php

namespace Kobami\Khelpers\Console\Commands;

use Illuminate\Console\Command;
use DB;

class WPRenameSite extends Command
{
    /**
     * wp-cli search replace examples
     *
     *
     *   # Search and replace but skip one column
     *   $ wp search-replace 'http://example.test' 'http://example.com' --skip-columns=guid
     *
     *   # Run search/replace operation but dont save in database
     *   $ wp search-replace 'foo' 'bar' wp_posts wp_postmeta wp_terms --dry-run
     *
     *   # Run case-insensitive regex search/replace operation (slow)
     *   $ wp search-replace '\[foo id="([0-9]+)"' '[bar id="\1"' --regex --regex-flags='i'
     *
     *   # Turn your production multisite database into a local dev database
     *   $ wp search-replace --url=example.com example.com example.test 'wp_*options' wp_blogs
     *
     *   # Search/replace to a SQL file without transforming the database
     *   $ wp search-replace foo bar --export=database.sql
     *
     *   # Bash script: Search/replace production to development url (multisite compatible)
     *   #!/bin/bash
     *   if $(wp --url=http://example.com core is-installed --network); then
     *       wp search-replace --url=http://example.com 'http://example.com' 'http://example.test' --recurse-objects --network --skip-columns=guid --skip-tables=wp_users
     *   else
     *       wp search-replace 'http://example.com' 'http://example.test' --recurse-objects --skip-columns=guid --skip-tables=wp_users
     *   fi
     */


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'khelpers:wp-rename-site';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'WordPress - rename site in DB. Requires wp-cli.';

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
        $this->line('');
        $this->warn('WARNING: replacing database text!');
        $this->line('');

        $wpOptionsTable = env('WP_OPTIONS_TABLE', 'wp_options');

        $siteInDb = DB::table($wpOptionsTable)->where('option_name', 'siteurl')->value('option_value');
        $siteInEnv = config('app.url');

        $searchFor = $this->ask('Search for?', $siteInDb);
        $replaceWith = $this->ask('Replace with?', $siteInEnv);

        $this->info('replace: "' . $searchFor . '" with: "' . $replaceWith . '"');

        // for SQLite
        if (env('DB_CONNECTION') === 'sqlite') {
            $this->renameInSQLiteDb($searchFor, $replaceWith);
        } else {
            $this->renameInMySQLDb($searchFor, $replaceWith);
        }
    }

    private function renameInSQLiteDb($searchFor, $replaceWith)
    {
        $db = env('DB_DATABASE');

        $this->line('Dumping SQLite db: ' . $db);
        shell_exec("sqlite3 $db .dump > site.sql");

        $this->line('Replacing...');
        shell_exec("sed -i 's?$searchFor?$replaceWith?' site.sql");

        $this->line('Importing...');
        shell_exec("rm $db && cat site.sql | sqlite3 $db");

        $this->info('Done!');
    }

    private function renameInMySQLDb($searchFor, $replaceWith)
    {
        // for MySQL
        $tablePrefix = env('WP_TABLE_PREFIX', 'wp_');
        $options = ' --recurse-objects --skip-columns=guid --skip-tables=' . $tablePrefix . 'users';
        $cmd = "cd public;../vendor/bin/wp search-replace '$searchFor' '$replaceWith' $options";

        $this->line('cmd:');
        $this->line($cmd);

        // first we'll do a dry run to review
        echo shell_exec($cmd . ' --dry-run');
        $this->info('Dry run done!');

        // confirm running command on real db
        $continue = $this->choice('Perform replace on database?', ['No', 'Yes'], 0);

        if ($continue === 'Yes') {
            echo shell_exec($cmd);

            $this->info('Done!');
        } else {
            $this->error('Aborted! Database not changed.');
        }
    }
}
