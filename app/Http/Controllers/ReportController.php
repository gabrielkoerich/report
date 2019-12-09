<?php

namespace App\Http\Controllers;

use App\Report;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

class ReportController
{
    /**
     * List reports.
     */
    public function index(): Collection
    {
        return Report::all();
    }

    /**
     * Gat a report.
     */
    public function show(Report $report): array
    {
        return [
            'report' => $report,
            'data' => $report->getData(),
        ];
    }

    /**
     * Create a new report.
     */
    public function create(Request $request): Report
    {
        return Report::create($request->all());
    }

    /**
     * Update a given report.
     */
    public function update(Request $request, Report $report): Report
    {
        return $report->update($request->all());
    }

    /**
     * Update a given report.
     */
    public function delete(Request $request, Report $report): void
    {
        $report->delete();
    }
}
