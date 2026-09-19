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
    protected $signature = 'make:admin {email : The email of the user} {--password= : Set or create user with this password} {--name= : Name if creating a new user} {--revoke : Revoke admin access instead}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grant or revoke admin rights for a user by email, or create a new admin';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (! $user) {
            $password = $this->option('password');
            if ($password) {
                $name = $this->option('name') ?: 'Administrator';
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => $password,
                    'is_admin' => true,
                    'plan' => 'pro',
                ]);
                $this->info("สร้างบัญชีแอดมินใหม่ {$name} ({$email}) เรียบร้อยแล้ว");

                return self::SUCCESS;
            }

            $this->error("ไม่พบบัญชีผู้ใช้ที่มีอีเมล: {$email} (หากต้องการสร้างใหม่ ให้ระบุ --password=...)");

            return self::FAILURE;
        }

        if ($this->option('password')) {
            $user->update(['password' => $this->option('password')]);
            $this->info("อัปเดตรหัสผ่านของ {$user->name} ({$email}) เรียบร้อยแล้ว");
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
