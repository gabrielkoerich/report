<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Report;

class ReportTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function testBasicTest()
    {
        $reports = Report::all();

        dd($reports);
    }
}
