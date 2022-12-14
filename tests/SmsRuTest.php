<?php

namespace hanymigame\Tests;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use hanymigame\SmsRuChannel\Channels\SmsRuChannel;
use hanymigame\SmsRuChannel\Exceptions\ResponseException;
use hanymigame\SmsRuChannel\Messages\SmsRuMessage;
use hanymigame\SmsRuChannel\SmsRuApi;
use PHPUnit\Framework\TestCase;
use Mockery as M;

class SmsRuTest extends TestCase
{
    private SmsRuApi|M\MockInterface $smsRu;
    private SmsRuChannel $channel;


    public function setUp(): void
    {
        $this->smsRu = M::mock(SmsRuApi::class, [
            'api_key' => 'test'
        ]);

        $this->channel = new SmsRuChannel($this->smsRu);
    }

    public function tearDown(): void
    {
        M::close();
    }

    /**
     * @throws GuzzleException
     * @throws ResponseException
     */
    public function testCanSendNotification(): void
    {
        $this->smsRu->shouldReceive('send')
            ->once()
            ->with([
                'to'  => '+12345678901',
                'msg' => 'test message'
            ]);

        $this->channel->send(new TestNotifiable(), new TestNotification());
    }
}

class TestNotifiable
{
    use Notifiable;

    public function routeNotificationForSmsRu(): string
    {
        return '+12345678901';
    }
}

class TestNotification extends Notification
{
    public function toSmsRu(): SmsRuMessage
    {
        return new SmsRuMessage('test message');
    }
}