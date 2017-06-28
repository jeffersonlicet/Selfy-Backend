<?php

namespace App\Notifications;

use App\Models\Photo;
use FCM;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;

/**
 * @property Photo photo
 */
class PlayNotification extends Notification
{
    use Queueable;
    private $photo;

    /**
     * Create a new notification instance.
     *
     * @param $photo
     * @internal param Photo $photo
     */
    public function __construct(Photo $photo)
    {
        $this->$photo = $photo;
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
        if($notifiable->firebase_token != null)
        {
            $optionBuiler = new OptionsBuilder();
            $optionBuiler->setTimeToLive(60*20)->setPriority("high");

            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData(['object' => $this->photo->photo_id, 'type' => 'play']);
            $notificationBuilder = new PayloadNotificationBuilder();
            $notificationBuilder->setTitle('Selfy')
                ->setBody('New play completed')
                ->setSound('clean_selfy');

            $option = $optionBuiler->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();

            FCM::sendTo($notifiable->firebase_token, $option, $notification, $data);
        }

        return [
            'photo_id' => $this->photo->photo_id,
        ];
    }
}
