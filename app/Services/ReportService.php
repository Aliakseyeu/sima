<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 28.10.18
 * Time: 13:25
 */

namespace App\Services;


use App\Group;
use App\Objects\Report\Report;

class ReportService
{

    public function create(Group $group): Report
    {
        $report = new Report();
        foreach ($group->orders as $order){
            foreach($order->users as $user){
                $report->addUser($user);
            }
        }
        return $report;
    }

}