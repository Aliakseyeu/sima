<?php

use App\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createRole('user', 'Пользователь');
        $this->createRole('admin', 'Администратор');
    }
    
    protected function createRole(string $slug, string $name): void
    {
		$role = new Role();
		$role->slug = $slug;
		$role->name = $name;
		$role->save();
	}
}
