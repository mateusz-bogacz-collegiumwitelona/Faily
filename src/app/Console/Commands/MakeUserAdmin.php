<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class MakeUserAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:make-admin {email : User email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grant admin privileges to a user based on email address';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        if (!$email) {
            $this->error('You must specify an email address!');
            return 1;
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email: {$email} doesn't exist");

            return 1;
        }

        if ($user->role === 'admin') {
            $this->warn("️User {$user->name} ({$email}) already has admin privileges.");
            return 0;
        }

        if (!$this->confirm("Do you want to give {$user->name} admin privileges?")) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $oldRole = $user->role;
        $user->role = 'admin';
        $user->save();

        $this->info("Success! User {$user->name} ({$email}) now has admin privileges.");

        return 0;
    }
}
