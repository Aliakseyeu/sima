<?php

use App\Status;
use Illuminate\Database\Seeder;

class StatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->saveStatus('new', 'Новый');
        $this->saveStatus('archived', 'Архивный');
    }
    
    protected function saveStatus(string $slug, string $name): void
    {
		$status = new Status();
        $status->slug = $slug;
        $status->name = $name;
        $status->save();
	}
}
