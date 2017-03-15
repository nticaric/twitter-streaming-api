<?php

use Nticaric\Twitter\TwitterRestApi;

class TwitterStreamTest extends \PHPUnit_Framework_TestCase
{
    protected $api = null;

    public function setUp()
    {
        $defaults = [
            'proxy' => null,
        ];

        $this->api = new TwitterRestApi([
            'consumer_key'    => 'r5FaLMas0OHsxOTi9j5szaXaO',
            'consumer_secret' => 'eVOjwp5bpUrhyhws0KoxlIsQ2BhRssha5VcEhYM2DAyb2Zz3Hp',
            'token'           => '1243964444-8nUuJqrKaqvYXfkwl8TtgHbOnGtpIhUXaDVQtxq',
            'token_secret'    => 'k2AKEmWGR5KILc8qo5E7TJQx5tqKBQBQ32A3a7tJuYvzW',
        ], $defaults);
    }

    public function testPostStatusesUpdate()
    {
        try {
            $status = $this->api->postStatusesUpdate([
                'status' => 'This is a test status',
            ]);
            print_r($status);
        } catch (Exception $e) {
            var_dump($e->getMessage());
            $response = json_decode($e->getResponse()->getBody(), true);
            $this->assertEquals('No status found with that ID.', $response['errors'][0]['message']);
            $this->assertEquals(144, $response['errors'][0]['code']);
        }
    }

    public function testGetStatusesShow()
    {
        try {
            $status = $this->api->getStatusesShow([
                'id' => 'someid',
            ]);
        } catch (Exception $e) {
            $response = json_decode($e->getResponse()->getBody(), true);
            $this->assertEquals('No status found with that ID.', $response['errors'][0]['message']);
            $this->assertEquals(144, $response['errors'][0]['code']);
        }

        try {
            $status = $this->api->getStatusesShow([
                'id' => '742715344175783937',
            ]);
            $this->assertEquals($status['id_str'], '742715344175783937');
        } catch (Exception $e) {
            $response = json_decode($e->getResponse()->getBody(), true);
            $this->assertEquals('No status found with that ID.', $response['errors'][0]['message']);
        }
    }

    public function testGetUsersShow()
    {
        try {
            $user = $this->api->getUsersShow([
                'screen_name' => 'krowdster',
            ]);
        } catch (Exception $e) {
            $response = json_decode($e->getResponse()->getBody(), true);
            $this->assertEquals('User not found.', $response['errors'][0]['message']);
            $this->assertEquals(50, $response['errors'][0]['code']);
        }
    }
}
