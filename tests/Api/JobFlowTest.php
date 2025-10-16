<?php

namespace App\Tests\Api;

use App\Entity\ConversionJob;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class JobFlowTest extends WebTestCase
{
    public function test_convert_csv_to_json(): void
    {
        $client = self::createClient([], ['HTTP_ACCEPT' => 'application/json']);

        $csv = tempnam(sys_get_temp_dir(), 'csv');
        file_put_contents($csv, "a,b\n1,2\n3,4\n");

        $client->request('POST', '/jobs', [
            'output' => 'json'
        ], [
            'file' => [
                'tmp_name' => $csv,
                'name' => 'test.csv',
                'type' => 'text/csv',
                'size' => filesize($csv),
                'error' => UPLOAD_ERR_OK
            ]
        ]);

        $this->assertResponseStatusCodeSame(202);
        $id = json_decode($client->getResponse()->getContent(), true)['id'];

        $client->request('GET', "/jobs/$id");
        $this->assertResponseIsSuccessful();
        $payload = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame(ConversionJob::STATUS_DONE, $payload['status']);

        $client->request('GET', "/jobs/$id/result");
        $this->assertResponseIsSuccessful();
        $this->assertSame('application/json', $client->getResponse()->headers->get('content-type'));


        $data = json_decode($client->getResponse()->getContent(), true, JSON_THROW_ON_ERROR);
        $this->assertIsArray($data);
        // simple content check
        $this->assertEquals([
            ['a' => '1', 'b' => '2'],
            ['a' => '3', 'b' => '4'],
        ], $data);
    }
}
