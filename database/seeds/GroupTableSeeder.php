<?php

use App\Group;
use Illuminate\Database\Seeder;

class GroupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createGroup();
    }
    
    protected function createGroup(): void
    {
		$group = new Group();
		$group->save();
	}
}
