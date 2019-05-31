<?php

use App\{Role, User};
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class, 10)->create();
        User::findOrFail(1)->roles()->attach(Role::whereSlug('admin')->first());
    }
}
