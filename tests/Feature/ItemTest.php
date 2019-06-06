<?php

namespace Tests\Feature;

use App\{Group, User};
use Tests\Support\{Prepare, ItemTrait};
use \Illuminate\Foundation\Testing\TestResponse as Response;

class ItemTest extends Prepare
{

    use ItemTrait;
	
    protected $url;
    protected $group;
    protected $user;
    
    public function setUp(): void
    {
        parent::setUp();
        $this->group = Group::first();
        $this->url = $this->getItemCreateUrl($this->group->id);
        $this->user = User::findOrFail(1);
    }

    public function testNotAuthenticatedUserCanNotSeeItemSearchPage(): void
    {
        $response = $this->get($this->url);
        $response->assertRedirect('/login');
    }
    
    public function testUserCanNotSeeItemSearchPageWithoutGroup(): void
    {
        $response = $this->actingAs($this->user)->get($this->getItemCreateUrl());
        $response->assertStatus(404);
    }
    
    public function testAuthenticatedUserCanSeeItemSearchPage(): void
    {
        $response = $this->actingAs($this->user)->get($this->url);
        $response->assertOk();
    }
    
    public function testUserCanNotFindItemWithoutSid(): void
    {
        $response = $this->executeQuery(
            $this->getQueryData(null, $this->group->id)
        );
        $response->assertRedirect($this->url);
        $response->assertSessionHasErrors('sid');
	}
    
    public function testUserCanNotFindItemWithoutGroupId()
    {
        $response = $this->executeQuery(
            $this->getQueryData($this->getActualItem()->sid, null)
        );
        $response->assertRedirect($this->url);
        $response->assertSessionHasErrors('group');
    }
    
    public function testUserCanNotFindItemWithWrongSid()
    {
        $response = $this->executeQuery($this->getQueryData(1, $this->group->id));
        $this->assertTrue($response->exception instanceof \App\Exceptions\NotFoundException);
        $response->assertRedirect('/');
    }
    
    public function testUserCanFindItem()
    {
        $response = $this->executeQuery(
            $this->getQueryData($this->getActualItem()->sid, $this->group->id)
        );
        $response->assertOk();
        $response->assertSee('Количество');
    }
    
    protected function getQueryData(int $sid = null, int $group = null): array
    {
        return [
            'sid' => $sid,
            'group' => $group
        ];
    }
    
    protected function executeQuery(array $data): Response
    {
        return $this->from($this->url)
            ->actingAs($this->user)
            ->post('/item/show', $data);
    }
    
    protected function getItemCreateUrl(int $group = NULL): string
    {
        return '/item/create/'.$group;
    }
    
}