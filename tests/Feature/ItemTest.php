<?php

namespace Tests\Feature;


use \Illuminate\Foundation\Testing\TestResponse as Response;

class ItemTest extends BaseItem
{

    // TODO testUserCanNotFindExistentItemWithoutGroupId
    
//    protected $itemRepository;
//    protected $groupRepository;
//    protected $userRepository;
//
    protected $actualUrl;
//    
//
    public function setUp(): void
    {
        parent::setUp();
        $this->actualUrl = $this->getItemCreateUrl($this->getGroup()->id);
        
////        $this->userRepository = new UserRepository();
//        $this->itemRepository = new ItemRepository();
//        dd($this->itemRepository->getItems());
    }

    public function testNotAuthenticatedUserCanNotSeeItemSearchPage(): void
    {
        $response = $this->get($this->actualUrl);
        $response->assertRedirect('/login');
    }
    
    protected function getItemCreateUrl(int $group = NULL): string
    {
        return '/item/create/'.$group;
    }
    
    /*public function testAuthenticatedUserCanSeeItemSearchPage(): void
    {
        $response = $this->actingAs($this->getUser())->get($this->actualUrl);
        $response->assertOk();
    }
    
    public function testUserCanNotSeeItemSearchPageWithoutGroup(): void
    {
        $response = $this->actingAs($this->getUser())
            ->get($this->getItemCreateUrl());
        $response->assertStatus(404);
    }
    
    public function testUserCanNotFindItemWithoutSid(): void
    {
        $response = $this->executeQuery($this->actualUrl, $this->getQueryData(null, $this->getGroup()->id));
        $response->assertRedirect($this->actualUrl);
        $response->assertSessionHasErrors('sid');
    }
    
    public function testUserCanNotFindExistentItemWithoutGroupId()
    {
        $response = $this->executeQuery($this->actualItemUrl, $this->getQueryData($this->actualItem->sid, null));
        $response->assertRedirect($this->actualItemUrll);
        $response->assertSessionHasErrors('group');
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
    

//

//

//

//

//


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

    

    

    public function testUserCanNotFindExistentItemWithoutGroupId()
    {
        $response = $this->executeQuery($this->actualItemUrl, $this->getQueryData($this->actualItem->sid, null));
        $response->assertRedirect($this->actualItemUrll);
        $response->assertSessionHasErrors('group');
    }

    public function testUserCanNotFindNonExistentItem()
    {
        dd($this->from($this->actualItemUrl));
        $response = $this->from($this->actualItemUrl)
            ->authenticateByTest()
            ->post('/item/show', [
                'sid' => '1',
                'group' => $this->actualItemGroup->id
            ]);
        $response->assertSessionHasErrors(0);
        $this->assertTrue($response->exception instanceof \App\Exceptions\NotFoundException);
        $response->assertRedirect('/');
    }

    public function testUserCanFindExistentItem()
    {
        $response = $this->executeQuery($this->actualItemUrl, $this->getQueryData($this->actualItem->sid, $this->actualItemGroup->id));
        $response->assertOk();
        $response->assertViewIs('orders.create.exists');
        $response->assertSee('Количество');
    }

    public function testUserCanFindNotExistentItem()
    {
        $item = $this->getArchivedItem();
        $url = $this->getItemCreateUrl($this->actualGroup);
        $response = $this->executeQuery($url, $this->getQueryData($item->sid, $this->actualGroup->id));
        $response->assertOk();
        $response->assertViewIs('orders.create.new');
        $response->assertSee('Количество');
    }

    

    

    */
}
