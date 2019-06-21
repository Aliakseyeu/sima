<?php

namespace Tests\Feature;

use Tests\Support\{Prepare, UserTrait};

class ArchiveTest extends Prepare
{

    use UserTrait;

    protected $url = '/archive';

    public function testNotAuthedUserCanNotSeeArchivePage(): void
    {
        $response = $this->get($this->url);
        $response->assertRedirect('/login');
    }

    public function testAuthedUserCanSeeArchivePage(): void
    {
        $response = $this->actingAs($this->getUser())->get($this->url);
        $response->assertOk();
    }
    
}