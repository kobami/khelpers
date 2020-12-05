<?php

namespace Kobami\Khelpers\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'k:reset-passwords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'khelpers - Reset all user passwords';

    /**
     * Create a new command instance.
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
        $this->warn('Resetting all user passwords!');

        $password = $this->secret('New password [cancel]');

        if (! $password) {
            $this->warn('Nothing done.');

            return true;
        }

        $this->info(config('khelpers.app_type'));

        // use class from config because User model can be App\User or App\Models\User, etc
        $users = config('auth.providers.users.model')::all();
        $bar   = $this->output->createProgressBar(count($users));

        $users->each(function ($user) use ($password, &$bar) {

            if ($user->password) {
                $user->password = Hash::make($password);
                $user->save();
            } else if ($user->user_pass) {
                // wordpress
                $cmd = "cd public;wp user update $user->ID --user_pass=$password --skip-email";
                shell_exec($cmd);
            }

            $bar->advance();
        });

        $bar->finish();

        $this->line('');
        $this->line('');
        $this->info('Done!');

        return true;
    }
}
