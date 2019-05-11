<?php

namespace App\Http\Controllers;

use App\{Item};
use App\Http\Requests\SearchItemRequest;
use App\Repositories\ItemRepository;

class ItemController extends Controller
{
    
    protected $item;
    protected $itemRepository;

    public function __construct(Item $item, ItemRepository $itemRepository)
    {
        $this->item = $item;
        $this->itemRepository = $itemRepository;
    }

    public function create(int $group)
    {
        return view('orders.create.item', compact('group'));
    }

    public function show(SearchItemRequest $request)
    {
        $group = $request->group;
        $sid = $request->sid;
        $item = $this->item->getByGroupAndSid($group, $sid);
        $page = $request->page;
        $send = compact('page');
        if($item){
            $users = $item->order->users;
            return view('orders.create.exists', array_merge($send, compact('users', 'item')));
        } else {
            $item = $this->itemRepository->where('sid', $sid);
        }
        return view('orders.create.new', array_merge($send, compact('group', 'sid', 'item')));
    }

    public function update(int $id)
    {
        $item = $this->item->findOrFail($id);
        $info = $this->itemRepository->find($item->pid);
        if($item->update($this->item->getUpdateData($info))){
            return back()->withSuccess([__('messages.updated', ['name'=>__('messages.item')])]);
        }
        return back()->withErrors([__('messages.error.updated')]);
    }


}
