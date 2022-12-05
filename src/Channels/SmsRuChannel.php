<?php

namespace hanymigame\SmsRuChannel\Channels;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Notifications\Notification;
use hanymigame\SmsRuChannel\Exceptions\ResponseException;
use hanymigame\SmsRuChannel\Messages\SmsRuMessage;
use hanymigame\SmsRuChannel\SmsRuApi;

/**
 *
 */
class SmsRuChannel
{
    /**
     * @var SmsRuApi
     */
    protected SmsRuApi $client;
    protected $response;

    /**
     * @param SmsRuApi $client
     */
    public function __construct(SmsRuApi $client)
    {
        $this->client = $client;
    }

    /**
     * @throws ResponseException
     * @throws GuzzleException
     */
    public function send($notifiable, Notification $notification): ?array
    {
        if (!$to = $notifiable->routeNotificationFor('sms_ru_channel', $notification)) {
            return null;
        }

        // toSmsRu method in app/notification/?.php notification class ... (artisan make:notification)
        $message = $notification->{'toSmsRu'}($notifiable);

        if (is_string($message)) {
            $message = new SmsRuMessage($message);
        }

        $payload = [
            'to' => $to,
            'msg' => trim($message->content)
        ];

        if ($message->from) {
            $payload['from'] = $message->from;
        }

        $this->response = $this->client->send($payload);

        //dump($this->response);

        return $this->response;
    }
}

