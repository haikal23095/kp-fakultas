<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class HashExistingPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:hash-passwords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hash existing plain text passwords in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to hash existing passwords...');

        // Get all users
        $users = User::all();

        foreach ($users as $user) {
            // Check if password is already hashed (bcrypt hashes start with $2y$)
            if (!str_starts_with($user->password, '$2y$')) {
                $hashedPassword = Hash::make($user->password);

                // Update the password in database
                DB::table('Users')
                    ->where('Id_User', $user->Id_User)
                    ->update(['password' => $hashedPassword]);

                $this->info("Password hashed for user: {$user->email}");
            } else {
                $this->info("Password already hashed for user: {$user->email}");
            }
        }

        $this->info('All passwords have been hashed successfully!');
    }
}