<?php

namespace Tests\Feature;

use Tests\Support\Prepare;
use Tests\Support\ItemTrait;
use Tests\Support\UserTrait;
use \Illuminate\Foundation\Testing\TestResponse as Response;

class ItemTest extends BaseOrder
{
	
    protected $url;
    protected $itemUrl;
    
    public function setUp(): void
    {
        parent::setUp();
        $this->url = $this->getItemCreateUrl($this->getGroup()->id);
        $this->itemUrl = $this->getItemCreateUrl($this->getItem()->order->group->id);
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
            $this->url, 
            $this->getQueryData(null, $this->getGroup()->id)
        );
        $response->assertRedirect($this->url);
        $response->assertSessionHasErrors('sid');
	}
    
    public function testUserCanNotFindExistentItemWithoutGroupId()
    {
        $response = $this->executeQuery(
            $this->itemUrl, 
            $this->getQueryData($this->getItem()->sid, null)
        );
        $response->assertRedirect($this->itemUrl);
        $response->assertSessionHasErrors('group');
    }
    
    public function testUserCanNotFindItemWithWrongSid()
    {
        $response = $this->from($this->itemUrl)
            ->actingAs($this->getUser())
            ->post('/item/show', [
                'sid' => '1',
                'group' => $this->getItem()->order->group->id
            ]);
        $response->assertSessionHasErrors(0);
        $this->assertTrue($response->exception instanceof \App\Exceptions\NotFoundException);
        $response->assertRedirect('/');
    }
    
    public function testUserCanFindExistentItem()
    {
        $response = $this->executeQuery(
            $this->itemUrl, 
            $this->getQueryData($this->getItem()->sid, $this->getItem()->order->group->id)
        );
        $response->assertOk();
        $response->assertViewIs('orders.create.exists');
        $response->assertSee('Количество');
    }
    
    public function testUserCanFindNotExistentItem()
    {
        $item = $this->findItem();
        $response = $this->executeQuery(
            $this->getItemCreateUrl($this->getGroup()->id), 
            $this->getQueryData($item->sid, $this->getGroup()->id)
        );
        $response->assertOk();
        $response->assertViewIs('orders.create.new');
        $response->assertSee('Количество');
    }
    
    protected function getQueryData(int $sid = null, int $group = null): array
    {
        return [
            'sid' => $sid,
            'group' => $group
        ];
    }
    
    protected function executeQuery(string $url, array $data): Response
    {
        return $this->from($url)
            ->actingAs($this->getUser())
            ->post('/item/show', $data);
    }
    
    protected function getItemCreateUrl(int $group = NULL): string
    {
        return '/item/create/'.$group;
    }
    



    /*protected $gropRepository;
    protected $itemRepository;
    protected $userRepository;

    protected $actualGroup;
    protected $actualUrl;
    protected $actualItem;
    protected $actualItemGroup;
    protected $actualItemUrl;

    public function __construct()
    {
        $this->groupRepository = new GroupRepository();
        $this->itemRepository = new ItemRepository();
        $this->userRepository = new UserRepository();

        $this->actualGroup = $this->groupRepository->getActualGroup();
        $this->actualUrl = $this->getItemCreateUrl($this->actualGroup);
        $this->actualItem = $this->itemRepository->getActualItem();
        $this->actualItemGroup = $this->actualItem->order->group;
        $this->actualItemUrl = $this->getItemCreateUrl($this->actualItemGroup);
    }

    


    

    

    

    

    

    */
}
