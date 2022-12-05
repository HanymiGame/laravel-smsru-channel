# Sms.ru notification for Laravel

## Install

```Bash
composer require hanymigame/laravel-smsru-channel
php artisan vendor:publish --provider="hanymigame\SmsRuChannel\SmsRuServiceProvider"
```
Add to .env file:

```
SMS_RU_API_KEY=you_api_key
SMS_RU_SENDER=
SMS_RU_TRANSLIT=0
SMS_RU_TEST=1
SMS_RU_PARTNER_ID=0
```

## Usage
```Bash
php artisan make:notification SMSNotification
```

You can use the channel in your `via()` method inside the notification:

```php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use hanymigame\SmsRuChannel\Messages\SmsRuMessage;
use hanymigame\SmsRuChannel\Channels\SmsRuChannel;

class SMSNotification extends Notification
{
    use Queueable;
    
    public function __construct()
    {
        //
    }
    
    public function via($notifiable)
    {
        return [SmsRuChannel::class];
    }

    public function toSmsRu($notifiable)
    {
        return new SmsRuMessage("Привет! Это тестовое СМС.");
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
```

Add in controller, model or command class:

```php
Notification::route('sms_ru_channel', '79301579978')->notify(new SMSNotification());
```
Edit: app/Providers/EventServiceProvider.php

```php
use Illuminate\Notifications\Events\NotificationSent;
use App\Listeners\ResponseNotification;
use App\Listeners\CheckNotificationStatus;
use Illuminate\Notifications\Events\NotificationSending;

class EventServiceProvider extends ServiceProvider
{

protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        NotificationSending::class => [
            CheckNotificationStatus::class,
        ],
        NotificationSent::class => [
            ResponseNotification::class,
        ],
    ];
    
    ...
    
}
```
Use command:

```Bash
php artisan event:generate
php artisan optimize:clear
```

Add to app/Listeners/ResponseNotification.php

```php
public function handle(NotificationSent $event)
    {
        // $event->channel
        // $event->notifiable
        // $event->notification
        // $event->response
        dump($event->response); // Get response status
    }
```
