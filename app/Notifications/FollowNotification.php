<?php

namespace App\Notifications;

use App\Models\Photo;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use Log;

/**
 * @property Photo photo
 */
class FollowNotification extends Notification
{
    use Queueable;
    private $user;

    /**
     * Create a new notification instance.
     *
     * @param User $user
     * @internal param Photo $photo
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        Log::info($notifiable->firebase_token);
        if($notifiable->firebase_token != null)
        {
            $optionBuiler = new OptionsBuilder();
            $optionBuiler->setTimeToLive(60*20);

            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData(['a_data' => 'my_data']);

            $notificationBuilder = new PayloadNotificationBuilder();
            $notificationBuilder->setTitle('title')
                ->setBody('body')
                ->setSound('sound')
                ->setBadge('badge');

            $option = $optionBuiler->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();

            $downstreamResponse = FCM::sendTo($notifiable->firebase_token, $option, $notification, $data);
            Log::info($downstreamResponse->numberSuccess());
        }

        return [
            'user_id' => \Auth::user()->user_id,
        ];
    }
}
