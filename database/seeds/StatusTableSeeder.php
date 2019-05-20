<?php

use App\Status;
use Illuminate\Database\Seeder;

class StatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $status = new Status();
        $status->slug = 'new';
        $status->name = 'Новый';
        $status->save();
        
        $status = new Status();
        $status->slug = 'archived';
        $status->name = 'Архивный';
        $status->save();
    }
}
