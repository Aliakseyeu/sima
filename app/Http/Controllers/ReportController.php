<?php

namespace App\Http\Controllers;


use App\Group;
use App\Services\ReportService;

class ReportController extends Controller
{

    protected $group;
    protected $reportService;

    public function __construct(Group $group, ReportService $reportService)
    {
        $this->group = $group;
        $this->reportService = $reportService;
    }


    public function show(int $id)
    {
        $group = $this->group->findOrFail($id);
        $report = $this->reportService->create($group);
        return view('orders.report.report', compact('group', 'report'));
    }

}
