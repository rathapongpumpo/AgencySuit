<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin {email : The email of the user} {--revoke : Revoke admin access instead}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grant or revoke admin rights for a user by email';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("ไม่พบบัญชีผู้ใช้ที่มีอีเมล: {$email}");

            return self::FAILURE;
        }

        if ($this->option('revoke')) {
            $user->update(['is_admin' => false]);
            $this->info("ปลดสิทธิ์แอดมินของ {$user->name} ({$email}) เรียบร้อยแล้ว");
        } else {
            $user->update(['is_admin' => true]);
            $this->info("แต่งตั้ง {$user->name} ({$email}) เป็นแอดมินเรียบร้อยแล้ว");
        }

        return self::SUCCESS;
    }
}
