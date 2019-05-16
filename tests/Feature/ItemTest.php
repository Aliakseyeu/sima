<?php

namespace Tests\Feature;

use Tests\TestCase;

use \Illuminate\Foundation\Testing\TestResponse as Response;
use Tests\Feature\{
    GroupRepository,
    ItemRepository,
    UserRepository
};

class ItemTest extends TestCase
{

    // TODO testUserCanNotFindExistentItemWithoutGroupId
    
    protected $itemRepository;
    protected $groupRepository;
    protected $userRepository;

    protected $actualGroup;
    protected $actualUrl;
    

    public function setUp(): void
    {
        parent::setUp();
        $this->groupRepository = new GroupRepository();
        $this->actualGroup = $this->groupRepository->getActualGroup();
        $this->actualUrl = $this->getItemCreateUrl($this->actualGroup);
        $this->userRepository = new UserRepository();
    }

    public function testNotAuthenticatedUserCanNotSeeItemSearchPage(): void
    {
        $response = $this->get($this->actualUrl);
        $response->assertRedirect('/login');
    }

    public function testAuthenticatedUserCanSeeItemSearchPage(): void
    {
        $response = $this->actingAs($this->userRepository->store()->getUser())->get($this->actualUrl);
        $response->assertOk();
    }

    public function testUserCanNotSeeItemSearchPageWithoutGroup(): void
    {
        $response = $this->actingAs($this->userRepository->store()->getUser())
            ->get($this->getItemCreateUrl($this->groupRepository->getEmptyGroup()));
        $response->assertStatus(404);
    }

    public function testUserCanNotFindItemWithoutSid(): void
    {
        $response = $this->executeQuery($this->actualUrl, $this->getQueryData('', $this->actualGroup->id));
        $response->assertRedirect($this->actualUrl);
        $response->assertSessionHasErrors('sid');
    }

    public function testUserCanNotFindExistentItemWithoutGroupId()
    {
        $response = $this->executeQuery($this->actualItemUrl, $this->getQueryData($this->actualItem->sid, null));
        $response->assertRedirect($this->actualItemUrll);
        $response->assertSessionHasErrors('group');
    }

    protected function executeQuery(string $url, array $data): Response
    {
        return $this->from($url)
            ->actingAs($this->userRepository->store()->getUser())
            ->post('/item/show', $data);
    }

    protected function getQueryData(int $sid, int $group): array
    {
        return [
            'sid' => $sid,
            'group' => $group
        ];
    }

    protected fun ction getItemCreateUrl(Group $group): string
    {
        return '/item/create/'.$group->id;
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
