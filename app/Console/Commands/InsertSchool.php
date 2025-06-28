<?php

namespace App\Console\Commands;

use App\Models\Maktab;
use App\Models\Role;
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
            'name' => 'MyExamly Platform'
        ]);

        $this->info("School '" . $school->name . "' " . ($school->wasRecentlyCreated ? "created." : "already exists."));
    }
}
