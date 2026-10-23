<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create {email?} {--password=} {--name=}';

    protected $description = 'Create or promote an administrator account';

    public function handle(): int
    {
        $email = $this->argument('email') ?? $this->ask('Admin email');
        $name = $this->option('name') ?? $this->ask('Admin name', 'Baruna Dwi Cahya');
        $password = $this->option('password') ?? $this->secret('Admin password');

        if (! $email || ! $password) {
            $this->error('Email and password are required.');

            return self::FAILURE;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->info("Administrator {$user->email} configured successfully.");

        return self::SUCCESS;
    }
}
