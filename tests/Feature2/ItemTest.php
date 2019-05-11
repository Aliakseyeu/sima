<?php

namespace Tests\Feature;

use App\Group;

class ItemTest extends BaseOrderTest
{
    public function testNotAuthenticatedUserCanNotSeeItemSearchPage()
    {
        $group = $this->getActualGroup();
        $response = $this->get($this->getItemCreateUrl($group));
        $response->assertRedirect('/login');
    }

    public function testAuthenticatedUserCanSeeItemSearchPage()
    {

        $group = $this->getActualGroup();
        $response = $this->authenticateByTest()->get($this->getItemCreateUrl($group));
        $response->assertOk();
    }

    public function testUserCanNotSeeItemSearchPageWithoutGroup()
    {
        $response = $this->authenticateByTest()->get($this->getItemCreateUrl(new Group));
        $response->assertStatus(404);
    }

    public function testUserCanNotFindItemWithoutSid()
    {
        $group = $this->getActualItem()->order->group->id;
        $url = '/item/create/'.$group;
        $response = $this->from($url)->authenticateByTest()->post('/item/show', [
            'sid' => '',
            'group' => $group
        ]);
        $response->assertRedirect($url);
        $response->assertSessionHasErrors('sid');
    }

    public function testUserCanNotFindNonExistentItem()
    {
        // dd($this->getActualItem());
        $group = $this->getActualItem()->order->group->id;
        $response = $this->from('/item/create/'.$group)
            ->authenticateByTest()
            ->post('/item/show', [
                'sid' => '1',
                'group' => $group
            ]);
        $response->assertSessionHasErrors(0);
        $this->assertTrue($response->exception instanceof \App\Exceptions\NotFoundException);
        $response->assertRedirect('/');
    }

    public function testUserCanNotFindExistentItemWithoutGroupId()
    {
        $item = $this->getActualItem();
        $url = '/item/create/'.$item->order->group->id;
        $response = $this->from($url)->authenticateByTest()->post('/item/show', [
            'sid' => $item->sid
        ]);
        $response->assertRedirect($url);
        $response->assertSessionHasErrors('group');
    }

    public function testUserCanFindExistentItem()
    {
        $item = $this->getActualItem();
        $group = $item->order->group->id;
        $response = $this->from('/item/create/'.$group)->authenticateByTest()->post('/item/show', [
            'sid' => $item->sid,
            'group' => $group
        ]);
        $response->assertOk();
        $response->assertViewIs('orders.create.exists');
        $response->assertSee('Количество');
    }

    public function testUserCanFindNotExistentItem()
    {
        $item = $this->getArchivedItem();
        $group = $this->getActualGroup()->id;
        $response = $this->from('/item/create/'.$group)->authenticateByTest()->post('/item/show', [
            'sid' => $item->sid,
            'group' => $group
        ]);
        $response->assertOk();
        $response->assertViewIs('orders.create.new');
        $response->assertSee('Количество');
    }
}
