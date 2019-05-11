<?php

namespace Tests\Feature;

use \Illuminate\Foundation\Testing\TestResponse as Response;

class OrderUpdateTest extends BaseOrderTest
{

    protected $updateUrl = '/order/update';
    
    public function testNotAuthtedUserCanNotSeeOrderUpdateBtn(): void
    {
        $response = $this->get('/');
        $response->assertDontSee('Редактировать');
    }

    public function testNotAuthenticatedUserCanNotUpdateOrder(): void
    {
        $pivot = $this->getActualPivot();
        $response = $this->post($this->updateUrl, $this->getUpdateData($pivot));
        $this->isOrderNotUpdated($response, $pivot, '/login');
        // $this->assertEquals($pivot->qty, $this->getActualPivot()->qty);
    }

    public function testNotAuthorizedUserCanNotUpdateOrder(): void
    {
        $pivot = $this->getActualPivot();
        $response = $this->actingAs($this->getOrdinaryRoleUser())->post($this->updateUrl, $this->getUpdateData($pivot));
        $this->isOrderNotUpdated($response, $pivot);
        // $this->assertEquals($pivot->qty, $this->getActualPivot()->qty);
    }

    public function testAdminUserCanUpdateOrder(): void
    {
        $pivot = $this->getActualPivot();
        $response = $this->actingAs($this->getAdminRoleUser())->post($this->updateUrl, $this->getUpdateData($pivot));
        $this->isOrderUpdated($response, $pivot);
        $this->restoreUpdatedOrder($pivot);
    }

    public function testUserCanUpdateSelfOrder(): void
    {
        // $pivot = $this->getActualPivot();
        // dd($pivot->users);
        // $response = $this->actingAs()->post($this->updateUrl, $this->getUpdateData($pivot));
        // $this->isOrderUpdated($response, $pivot);
        // $this->restoreUpdatedOrder($pivot);
    }

    protected function getUpdateData(object $pivot): array
    {
        return [
            'id' => $pivot->id,
            'qty' => $pivot->qty + 1
        ];
    }

    protected function isOrderNotUpdated(Response $response, object $pivot, $url = '/'): void
    {
        $response->assertRedirect($url);
        $this->assertEquals($pivot->qty, $this->getActualPivot()->qty);
        $this->assertTrue($response->exception instanceof \Illuminate\Auth\AuthenticationException);
    }

    protected function isOrderUpdated(Response $response, object $pivot): void
    {
        $response->assertRedirect('/');
        $this->assertNotEquals($pivot, $updatedPivot = $this->getActualPivot());
        $this->assertEquals($pivot->qty + 1, $updatedPivot->qty);
    }

    protected function getActualPivot(): object
    {
        return $this->getActualItem()->order->users->first()->pivot;
    }

}
