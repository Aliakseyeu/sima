<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 01.11.18
 * Time: 14:58
 */

namespace App\Services;

use Auth;
use App\Exceptions\NotEmptyException;
use App\Group;
use App\Status;
use Carbon\Carbon;

class GroupService extends Service
{

    public function destroy(Group $group): bool
    {
        $this->isAuthorized();
        if($group->orders->count() > 0){
            throw new NotEmptyException('group');
        }
        return $group->delete();
    }

    public function toArchive(Group $group, Status $status): bool
    {
        $group->status_id = $status->archived()->id;
        $group->processed_at = Carbon::now();
        $group->user_id = Auth::id();
        return $group->save();
    }

}