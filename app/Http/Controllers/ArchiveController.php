<?php

namespace App\Http\Controllers;

use App\Services\GroupService;
use App\Status;

class ArchiveController extends Controller
{
    
    protected $status;
    protected $groupService;

    public function __construct(GroupService $groupService, Status $status)
    {
        $this->groupService = $groupService;
        $this->status = $status;
    }

    public function index()
    {
        $groups = $this->status->archived()->groups()->latest()->paginate(1);
        return view('orders.list.archived', compact('groups'));
    }

    public function store(int $id)
    {
        $group = $this->status->new()->groups()->findOrFail($id);
        if($this->groupService->toArchive($group, $this->status)){
            return redirect('/')->withSuccess([__('messages.updated', ['name'=>__('messages.group')])]);
        }
        return redirect('/')->withErrors([__('messages.error.updated')]);
    }

}
