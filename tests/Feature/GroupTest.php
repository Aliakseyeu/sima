<?php

namespace Tests\Feature;

use Tests\Support\{Prepare, UserTrait};

class GroupTest extends Prepare
{

    use UserTrait;

    protected $url = '/group/store';

    public function testUserCanNotStoreGroup(): void
    {
        $response = $this->actingAs($this->getUser())->get($this->url);
        
    }

    public function testAdminCanStoreGroup(): void
    {
        
    }

    public function testAdminCanNotDestroyNotEmptyGroup(): void
    {
        
    }

    public function testAdminCanDestroyEmptyGroup(): void
    {
        
    }

    public function testUserCanNotDestroyEmptyGroup(): void
    {
        
    }

    public function testUserCanNotArchiveGroup(): void
    {
        
    }

    public function testAdminCanArchiveGroup(): void
    {
        
    }
    
}