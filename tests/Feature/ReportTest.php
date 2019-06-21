<?php

namespace Tests\Feature;

use App\{Group, User};
use Tests\Support\{Prepare, GroupTrait, UserTrait};
use \Illuminate\Foundation\Testing\TestResponse as Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReportTest extends Prepare
{

    use GroupTrait;
    use UserTrait;

    protected $url = '/report/show/';

    public function testNotAuthedUserCanNotSeeReportPage(): void
    {
        $response = $this->get($this->url . $this->getGroup()->id);
        $response->assertRedirect('/login');

    }

    public function testUserCanSeeReportPage(): void
    {
        $response = $this->query($this->getUser(), $this->getGroup());
        $response->assertOk();
    }

    public function testUserCanNotSeeReportPageWithoutGroup(): void
    {
        $response = $this->actingAs($this->getUser())->get($this->url);
        $response->assertStatus(404);
    }

    public function testUserCanNotSeeReportPageWithWrongGroup(): void
    {
        $group = factory(Group::class)->make([
            'id' => 0
        ]);
        $response = $this->query($this->getUser(), $group);
        $response->assertRedirect('/');
        $this->assertTrue($response->exception instanceof ModelNotFoundException);
    }

    protected function query(User $user, Group $group): Response
    {
        return $this->actingAs($user)->get($this->url . $group->id);
    }
    
}