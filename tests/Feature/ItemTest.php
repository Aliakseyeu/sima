<?php

namespace Tests\Feature;

use App\{Group, User};
use Tests\Support\{Prepare, GroupTrait, ItemTrait, UserTrait};
use \Illuminate\Foundation\Testing\TestResponse as Response;

class ItemTest extends Prepare
{

    use GroupTrait;
    use ItemTrait;
    use UserTrait;
	
    protected $url;
    
    public function setUp(): void
    {
        parent::setUp();
        $this->url = $this->getItemCreateUrl($this->getGroup()->id);
    }

    public function testNotAuthenticatedUserCanNotSeeItemSearchPage(): void
    {
        $response = $this->get($this->url);
        $response->assertRedirect('/login');
    }
    
    public function testUserCanNotSeeItemSearchPageWithoutGroup(): void
    {
        $response = $this->actingAs($this->getUser())->get($this->getItemCreateUrl());
        $response->assertStatus(404);
    }
    
    public function testAuthenticatedUserCanSeeItemSearchPage(): void
    {
        $response = $this->actingAs($this->getUser())->get($this->url);
        $response->assertOk();
    }
    
    public function testUserCanNotFindItemWithoutSid(): void
    {
        $response = $this->executeQuery(
            $this->getQueryData(null, $this->getGroup()->id)
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
        $response = $this->executeQuery($this->getQueryData(1, $this->getGroup()->id));
        $this->assertTrue($response->exception instanceof \App\Exceptions\NotFoundException);
        $response->assertRedirect('/');
    }
    
    public function testUserCanFindItem()
    {
        $response = $this->executeQuery(
            $this->getQueryData($this->getActualItem()->sid, $this->getGroup()->id)
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
            ->actingAs($this->getUser())
            ->post('/item/show', $data);
    }
    
    protected function getItemCreateUrl(int $group = NULL): string
    {
        return '/item/create/'.$group;
    }
    
}