<?php

namespace App\Http\Controllers;

use App\{
    Group, Status,
    Objects\Response\IResponsable
};
use App\Http\Requests\{GroupUpdateRequest};
use App\Services\{CartService, GroupService};
use Illuminate\Http\{Response, RedirectResponse};

class GroupController extends Controller
{

    protected $group;
    protected $status;
    protected $cartService;
    protected $groupService;

    public function __construct(Group $group, Status $status, CartService $cartService, GroupService $groupService)
    {
        $this->group = $group;
        $this->status = $status;
        $this->cartService = $cartService;
        $this->groupService = $groupService;
    }

    public function store(){
        if($this->group->save()){
            return redirect('/')->withSuccess([__('messages.stored', ['name'=>__('messages.group')])]);
        }
        return redirect('/')->withErrors([__('messages.error.stored')]);
    }

    public function toCart(int $id)
    {
        $orders = $this->group->findOrFail($id)->orders;
        $response = $this->cartService->put($orders);
        return $this->getRedirect($response);
    }

    public function destroy(int $id)
    {
        $group = $this->status->new()->groups()->findOrFail($id);
        if($this->groupService->destroy($group)){
            return redirect('/')->withSuccess([__('messages.destroyed', ['name'=>__('messages.group')])]);
        }
        return back()->withErrors([__('messages.error.destroyed')]);
    }

    public function show(int $id, Response $response)
    {
        $group = $this->group->findOrFail($id);
        $page = $response->page ?? 1;
        return view('group.show', compact('group', 'page'));
    }

    public function update(GroupUpdateRequest $request)
    {
        $group = $this->status->new()->groups()->findOrFail($request->id);
        $group->comment = $request->comment;
        if($group->save()){
            return redirect('/?page='.$request->page)->withSuccess([__('messages.updated', ['name'=>__('messages.group')])]);
        }
        return redirect('/?page='.$request->page)->withErrors([__('messages.error.updated', ['name'=>__('messages.group')])]);
    }

    protected function getRedirect(IResponsable $response): RedirectResponse
    {
        $redirect = back();
        if(count($response->getErrors()) > 0){
            $redirect = $redirect->withErrors($response->getErrors());
        }
        if(count($response->getSuccess()) > 0){
            $redirect = $redirect->withSuccess($response->getSuccess());
        }
        return $redirect;
    }

}
