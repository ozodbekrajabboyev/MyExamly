<?php

namespace App\Console\Commands;

use App\Models\Maktab;
use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class InsertSchool extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
            protected $signature = 'app:insert-school';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert default roles and a dummy school';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Insert roles
        $roles = ['teacher', 'admin', 'superadmin'];

        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $this->info("Role '$roleName' " . ($role->wasRecentlyCreated ? "created." : "already exists."));
        }

        // Insert dummy school
        $school = Maktab::firstOrCreate([
            "region_id" => 14,
            "district_id" => 183,
            'name' => 'MyExamly Platform',
        ]);
        $this->info("School '" . $school->name . "' " . ($school->wasRecentlyCreated ? "created." : "already exists."));

        $user = User::firstOrCreate([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'maktab_id' => 1,
            'role_id' => 3,
            'password' => bcrypt('password'),
        ]);

        $this->info("✅ {$user->name} is now a superadmin successfully!");

    }
}
