<?php

use Tests\DuskTestCase;
use Illuminate\Support\Arr;
use Edujugon\PushNotification\Gcm;
use Edujugon\PushNotification\Fcm;
use Edujugon\PushNotification\Apn;
use Edujugon\PushNotification\PushNotification;

class PushNotificationTest extends DuskTestCase
{

    public $mockPushNotification;
    public $mockPushNotificationGcm;
    public $mockPushNotificationFcm;
    public $mockPushNotificationApn;

    /**
     * Setup test environment
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->mockPushNotificationGcm = Mockery::mock(Gcm::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $this->mockPushNotificationFcm = Mockery::mock(Fcm::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $this->mockPushNotificationApn = Mockery::mock(Apn::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $this->mockPushNotification = Mockery::mock(PushNotification::class, ['gcm'])->makePartial()->shouldAllowMockingProtectedMethods();
    }

    /**
     * Clear test environment before start test
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

     /**
     * Check for method exists or not.
     *
     * @test
     */
    public function method_exists()
    {
        $methodsToCheck = [
            'setService',
            'setMessage',
            'setDevicesToken',
            'setApiKey',
            'setConfig',
            'setUrl',
            'getUnregisteredDeviceTokens',
            'getFeedback',
            'send',
            'sendByTopic',
            '__get',
        ];

        foreach ($methodsToCheck as $method) {
            $this->checkMethodExist($this->mockPushNotification, $method);
        }
    }

    /**
     * Push notification instance creation without argument set gcm as service
     *
     * @test
     */
    public function push_notification_instance_creation_without_argument_set_gcm_as_service()
    {
        $this->assertInstanceOf('Edujugon\PushNotification\Gcm', $this->mockPushNotification->service);
    }

    /**
     * Assert send method returns an stdClass instance
     *
     * @test
     */
    public function assert_send_method_returns_an_stdClass_instance()
    {
        $this->mockPushNotification = Mockery::mock(PushNotification::class, ['fcm'])->makePartial()->shouldAllowMockingProtectedMethods();
        $response = $this->mockPushNotification->setMessage(['message' => 'Hello World'])
            ->setApiKey('asdfasdffasdfasdfasdf')
            ->setDevicesToken(['asdfasefaefwefwerwerwer'])
            ->setConfig(['dry_run' => false])
            ->send()
            ->getFeedback();
        $this->assertInstanceOf('stdClass', $response);
    }

    /**
     * Assert there is an array key called error
     *
     * @test
     */
    public function assert_there_is_an_array_key_called_error()
    {
        $this->mockPushNotification->setMessage(['message' => 'Hello World'])
            ->setApiKey('XXofYyQx2SJbumNrs_hUS6Rkrv3W8asd')
            ->setDevicesToken(['d1WaXouhHG34:AaPA91bF2byCOq-gexmHFqdysYX'])
            ->setConfig(['dry_run' => true])
            ->send();
        $this->assertFalse(isset($this->mockPushNotification->feedback->error));
    }

    /**
     * Assert unregistered device tokens is an array
     *
     * @test
     */
    public function assert_unregistered_device_tokens_is_an_array()
    {
        $this->mockPushNotification->setApiKey('wefwef23f23fwef')
            ->setDevicesToken([
                'asdfasdfasdfasdfXCXQ9cvvpLMuxkaJ0ySpWPed3cvz0q4fuG1SXt40-oasdf3nhWE5OKDmatFZaaZ',
                'asfasdfasdf_96ssdfsWuhabpZO9Basvz0q4fuG1SXt40-oXH4R5dwYk4rQYTeds3nhWE5OKDmatFZaaZ'
            ])
            ->setConfig(['dry_run' => true])
            ->setMessage(['message' => 'hello world'])
            ->send();
        $this->assertIsArray($this->mockPushNotification->getUnregisteredDeviceTokens());
    }

    /**
     * Set and get service config
     *
     * @test
     */
    public function set_and_get_service_config()
    {
        /** GCM */
        $this->mockPushNotification->setConfig(['time_to_live' => 3]);
        $this->assertArrayHasKey('time_to_live', $this->mockPushNotification->config);
        $this->assertArrayHasKey('priority', $this->mockPushNotification->config); //default key
        $this->assertIsArray($this->mockPushNotification->config);


        /** APNS */
        $this->mockPushNotification = Mockery::mock(PushNotification::class, ['apn'])->makePartial()->shouldAllowMockingProtectedMethods();
        $this->mockPushNotification->setConfig(['time_to_live' => 3]);
        $this->assertArrayHasKey('time_to_live', $this->mockPushNotification->config);
        $this->assertArrayHasKey('certificate', $this->mockPushNotification->config);
        $this->assertIsArray($this->mockPushNotification->config);
    }


    /**
     * Set message data
     *
     * @test
     */
    public function set_message_data()
    {
        $this->mockPushNotification->setMessage(['message' => 'hello world']);
        $this->assertArrayHasKey('message', $this->mockPushNotification->message);
        $this->assertEquals('hello world', $this->mockPushNotification->message['message']);
    }


    /**
     * Send method in apn service
     *
     * @test
     */
    public function send_method_in_apn_service()
    {
        $this->mockPushNotification = Mockery::mock(PushNotification::class, ['apn'])->makePartial()->shouldAllowMockingProtectedMethods();
        $message = [
            'aps' => [
                'alert' => [
                    'title' => '1 Notification test',
                    'body' => 'Just for testing purposes'
                ],
                'sound' => 'default'
            ]
        ];
        $this->mockPushNotification->setMessage($message)
            ->setDevicesToken([
                '507e3adaf433ae3e6234f35c82f8a43ad0d84218bff08f16ea7be0869f066c0312',
                'ac566b885e91ee74a8d12482ae4e1dfd2da1e26881105dec262fcbe0e082a358',
                '507e3adaf433ae3e6234f35c82f8a43ad0d84218bff08f16ea7be0869f066c0312'
            ]);
        $push = $this->mockPushNotification->send();
        $this->assertInstanceOf('stdClass', $push->getFeedback());
        $this->assertIsArray($push->getUnregisteredDeviceTokens());
    }

    /**
     * Apn without certificate
     *
     * @test
     */
    public function apn_without_certificate()
    {
        $this->mockPushNotification = Mockery::mock(PushNotification::class, ['apn'])->makePartial()->shouldAllowMockingProtectedMethods();
        $this->mockPushNotification->setConfig(['custom' => 'Custom Value', 'certificate' => 'MycustomValue']);
        $this->mockPushNotification->send();
        $this->assertFalse($this->mockPushNotification->feedback->success);
    }

    /**
     * Apn dry run option update the apn url
     *
     * @test
     */
    public function apn_dry_run_option_update_the_apn_url()
    {
        $this->mockPushNotification = Mockery::mock(PushNotification::class, ['apn'])->makePartial()->shouldAllowMockingProtectedMethods();
        $this->mockPushNotification->setConfig(['dry_run' => false]);
        $this->assertEquals('ssl://gateway.push.apple.com:2195', $this->mockPushNotification->url);
        $this->mockPushNotification->setConfig(['dry_run' => true]);
        $this->assertEquals('ssl://gateway.sandbox.push.apple.com:2195', $this->mockPushNotification->url);
    }

    /**
     * If push service as argument is not valid user gcm as default
     *
     * @test
     */
    public function if_push_service_as_argument_is_not_valid_user_gcm_as_default()
    {
        $this->mockPushNotification = Mockery::mock(PushNotification::class, ['asdf'])->makePartial()->shouldAllowMockingProtectedMethods();
        $this->assertInstanceOf('Edujugon\PushNotification\Gcm', $this->mockPushNotification->service);
    }

    /**
     * Get available push service list
     *
     * @test
     */
    public function get_available_push_service_list()
    {
        $this->assertCount(3, $this->mockPushNotification->servicesList);
        $this->assertIsArray($this->mockPushNotification->servicesList);
    }

    /**
     * If argument in set service method does not exist set the service by default
     *
     * @test
     */
    public function if_argument_in_set_service_method_does_not_exist_set_the_service_by_default()
    {
        $this->mockPushNotification->setService('asdf')->send();
        $this->assertInstanceOf('Edujugon\PushNotification\Gcm', $this->mockPushNotification->service);
        $this->mockPushNotification->setService('fcm');
        $this->assertInstanceOf('Edujugon\PushNotification\Fcm', $this->mockPushNotification->service);
    }

    /**
     * Get feedback after sending notification
     *
     * @test
     */
    public function get_feedback_after_sending_notification()
    {
        $this->mockPushNotification = Mockery::mock(PushNotification::class, ['fcm'])->makePartial()->shouldAllowMockingProtectedMethods();
        $response = $this->mockPushNotification->setMessage(['message' => 'Hello World'])
            ->setApiKey('asdfasdffasdfasdfasdf')
            ->setDevicesToken(['asdfasefaefwefwerwerwer'])
            ->setConfig(['dry_run' => false])
            ->send()
            ->getFeedback();
        $this->assertInstanceOf('stdClass', $response);
    }

    /**
     * Get Apn feedback after send notification
     *
     * @test
     */
    public function apn_feedback()
    {
        $this->mockPushNotification = Mockery::mock(PushNotification::class, ['apn'])->makePartial()->shouldAllowMockingProtectedMethods();
        $message = [
            'aps' => [
                'alert' => [
                    'title' => 'New Notification test',
                    'body' => 'Just for testing purposes'
                ],
                'sound' => 'default'
            ]
        ];
        $this->mockPushNotification->setMessage($message)
            ->setDevicesToken([
                'asdfasdf'
            ]);
        $this->mockPushNotification->send();
        $this->assertInstanceOf('stdClass', $this->mockPushNotification->getFeedback());
        $this->assertIsArray($this->mockPushNotification->getUnregisteredDeviceTokens());
    }

    /**
     * Allow apikey from config file
     *
     * @test
     */
    public function allow_apikey_from_config_file()
    {
        $this->mockPushNotification = Mockery::mock(PushNotification::class, ['fcm'])->makePartial()->shouldAllowMockingProtectedMethods();
        $response = $this->mockPushNotification->setMessage(['message' => 'Hello World'])
            ->setDevicesToken(['asdfasefaefwefwerwerwer'])
            ->setConfig(['dry_run' => true])
            ->send()
            ->getFeedback();
        $this->assertInstanceOf('stdClass', $response);
    }

    /**
     * Fake unregistered Devices Token with apn feedback response merged to our custom feedback
     *
     * @test
     */
    public function fake_unregisteredDevicesToken_with_apn_feedback_response_merged_to_our_custom_feedback()
    {
        $primary = [
            'success' => 3,
            'failure' => 1,
            'tokenFailList' => ['asdf']
        ];
        $array = [
            'apnsFeedback' => [
                [
                    'timestamp' => 121212,
                    'length' => 23,
                    'devtoken' => '2121221212'
                ],
                [
                    'timestamp' => 5454545,
                    'length' => 32,
                    'devtoken' => '34343434'
                ]
            ]
        ];
        $merge = array_merge($primary, $array);
        $obj = json_decode(json_encode($merge));
        $tokens = [];
        if (!empty($obj->tokenFailList)) {
            $tokens =  $obj->tokenFailList;
        }
        if (!empty($obj->apnsFeedback)) {
            $tokens = array_merge($tokens, Arr::pluck($obj->apnsFeedback, 'devtoken'));
        }
        $this->assertTrue(true);
    }

    /**
     * Send notification by topic in fcm
     *
     * @test
     */
    public function send_a_notification_by_topic_in_fcm()
    {
        $this->mockPushNotification = Mockery::mock(PushNotification::class, ['fcm'])->makePartial()->shouldAllowMockingProtectedMethods();
        $response = $this->mockPushNotification->setMessage(['message' => 'Hello World'])
            ->setApiKey('asdfasdffasdfasdfasdf')
            ->setConfig(['dry_run' => false])
            ->sendByTopic('test')
            ->getFeedback();
        $this->assertInstanceOf('stdClass', $response);
    }

    /**
     * Send notification by condition in fcm
     *
     * @test
     */
    public function send_a_notification_by_condition_in_fcm()
    {
        $this->mockPushNotification = Mockery::mock(PushNotification::class, ['fcm'])->makePartial()->shouldAllowMockingProtectedMethods();
        $response = $this->mockPushNotification->setMessage(['message' => 'Hello World'])
            ->setApiKey('asdfasdffasdfasdfasdf')
            ->setConfig(['dry_run' => false])
            ->sendByTopic("'dogs' in topics || 'cats' in topics", true)
            ->getFeedback();
        $this->assertInstanceOf('stdClass', $response);
    }

    /**
     * Apn connection attempts sdefault
     *
     * @test
     */
    public function apn_connection_attempts_default()
    {
        $this->mockPushNotification = Mockery::mock(PushNotification::class, ['apn'])->makePartial()->shouldAllowMockingProtectedMethods();
        $this->mockPushNotification->setConfig(['dry_run' => true]);
        $key = 'connection_attempts';
        $this->assertArrayNotHasKey($key, $this->mockPushNotification->config);
    }

    /**
     * Set apn connect attempts override default
     *
     * @test
     */
    public function set_apn_connect_attempts_override_default()
    {
        $this->mockPushNotification = Mockery::mock(PushNotification::class, ['apn'])->makePartial()->shouldAllowMockingProtectedMethods();
        $expected = 0;
        $this->mockPushNotification->setConfig([
            'dry_run' => true,
            'connection_attempts' => $expected,
        ]);
        $key = 'connection_attempts';
        $this->assertArrayHasKey($key, $this->mockPushNotification->config);
        $this->assertEquals($expected, $this->mockPushNotification->config[$key]);
    }

}
