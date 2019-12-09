<?php

namespace Tests\Feature;

use App\Report;
use App\Website;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Get the report data.
     */
    private function getReportData()
    {
        return [
            'model' => Website::class,
            'context' => [
                [
                    'field' => 'created_at',
                    'operator' => '=',
                    'query' => 'YEAR(NOW())',
                ],
                [
                    'field' => 'domain',
                    'operator' => 'ends',
                    'query' => '.net|.com',
                ]
            ]
        ];
    }

    public function testCreateReport()
    {
        $response = $this->post('/report', $this->getReportData());

        $response->assertStatus(201);

        $response = $this->get('/report');

        $response->assertStatus(200);

        $reports = json_decode($response->getContent());

        $this->assertEquals($reports[0]->model, Website::class);
    }

    public function testShowReport()
    {
        $domains = [
            ['domain' => 'testing.com', 'user_id' => 1],
            ['domain' => 'testing.net'],
            ['domain' => 'testing.gov'],
            ['domain' => 'testing.dev'],
        ];

        foreach ($domains as $domain) {
            Website::create($domain);
        }

        $response = $this->post('/report', $this->getReportData());

        $report = json_decode($response->getContent());

        $response->assertStatus(201);

        $response = $this->get('/report/' . $report->id);

        $response->assertStatus(200);

        $report = json_decode($response->getContent());

        // Only .net and .com domains
        $this->assertEquals($report->data[0]->domain, 'testing.com');
        $this->assertEquals($report->data[0]->user->email, 'testing@test.com');
        $this->assertEquals($report->data[1]->domain, 'testing.net');
        $this->assertNull($report->data[1]->user);
    }
}
