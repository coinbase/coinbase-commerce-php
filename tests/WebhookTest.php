<?php
namespace CoinbaseCommerce\Tests;

use PHPUnit\Framework\TestCase;
use CoinbaseCommerce\Webhook;
use CoinbaseCommerce\Resources\Event;

class WebhookTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testSuccessfullyVerifyBody()
    {
        $secret = '30291a20-0bd1-4267-9b0f-e6e7b123c0bf';
        $payload = '{"id":1,"scheduled_for":"2017-01-31T20:50:02Z","attempt_number":1,"event":{"id":"24934862-d980-46cb-9402-43c81b0cdba6","type":"charge:created","api_version":"2018-03-22","created_at":"2017-01-31T20:49:02Z","data":{"code":"66BEOV2A","name":"The Sovereign Individual","description":"Mastering the Transition to the Information Age","hosted_url":"https://commerce.coinbase.com/charges/66BEOV2A","created_at":"2017-01-31T20:49:02Z","expires_at":"2017-01-31T21:04:02Z","timeline":[{"time":"2017-01-31T20:49:02Z","status":"NEW"}],"metadata":{},"pricing_type":"no_price","payments":[],"addresses":{"bitcoin":"0000000000000000000000000000000000","ethereum":"0x0000000000000000000000000000000000000000","litecoin":"3000000000000000000000000000000000","bitcoincash":"bitcoincash:000000000000000000000000000000000000000000"}}}}';
        $headerSignature = '8be7742c7d372f08a6a3224edadf18a22b65fa9e28f3f2de97376cdaa092590d';

        $event = Webhook::buildEvent($payload, $headerSignature, $secret);

        $this->assertInstanceOf(Event::getClassName(), $event);
        $this->assertEquals('charge:created', $event->type);
    }

    /**
     * @expectedException \CoinbaseCommerce\Exceptions\SignatureVerificationException
     */
    public function testFailedOnInvalidSecretKey()
    {
        $invalidSecret = '30291a20-0bd1-4267-9b0f-e6e7b123c0bg';
        $payload = '{"id":1,"scheduled_for":"2017-01-31T20:50:02Z","attempt_number":1,"event":{"id":"24934862-d980-46cb-9402-43c81b0cdba6","type":"charge:created","api_version":"2018-03-22","created_at":"2017-01-31T20:49:02Z","data":{"code":"66BEOV2A","name":"The Sovereign Individual","description":"Mastering the Transition to the Information Age","hosted_url":"https://commerce.coinbase.com/charges/66BEOV2A","created_at":"2017-01-31T20:49:02Z","expires_at":"2017-01-31T21:04:02Z","timeline":[{"time":"2017-01-31T20:49:02Z","status":"NEW"}],"metadata":{},"pricing_type":"no_price","payments":[],"addresses":{"bitcoin":"0000000000000000000000000000000000","ethereum":"0x0000000000000000000000000000000000000000","litecoin":"3000000000000000000000000000000000","bitcoincash":"bitcoincash:000000000000000000000000000000000000000000"}}}}';
        $headerSignature = '8be7742c7d372f08a6a3224edadf18a22b65fa9e28f3f2de97376cdaa092590d';

        Webhook::buildEvent($payload, $headerSignature, $invalidSecret);
    }

    /**
     * @expectedException \CoinbaseCommerce\Exceptions\InvalidResponseException
     * @expectedExceptionMessage Invalid payload provided. No JSON object could be decoded
     */
    public function testThrowExceptionOnInvalidJsonPayload()
    {
        $secret = '30291a20-0bd1-4267-9b0f-e6e7b123c0bf';
        $invalidPayload = 'Not json';
        $headerSignature = '8be7742c7d372f08a6a3224edadf18a22b65fa9e28f3f2de97376cdaa092590d';

        Webhook::buildEvent($invalidPayload, $headerSignature, $secret);
    }

    /**
     * @expectedException \CoinbaseCommerce\Exceptions\InvalidResponseException
     * @expectedExceptionMessage Invalid payload provided.
     */
    public function testThrowExceptionOnNoEventPayload()
    {
        $secret = '30291a20-0bd1-4267-9b0f-e6e7b123c0bf';
        $invalidPayload = '{"id":1,"scheduled_for":"2017-01-31T20:50:02Z","attempt_number":1}';
        $headerSignature = '8be7742c7d372f08a6a3224edadf18a22b65fa9e28f3f2de97376cdaa092590d';

        Webhook::buildEvent($invalidPayload, $headerSignature, $secret);
    }
}
