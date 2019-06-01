<?php

use App\{Role, User};
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class, 10)->create();
        User::latest()->first()->roles()->attach(Role::whereSlug('admin')->first());
    }
}
