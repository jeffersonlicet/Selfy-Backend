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

/**
 * @property Photo photo
 */
class LikeNotification extends Notification
{
    use Queueable;
    private $user;
    private $photo_id;

    /**
     * Create a new notification instance.
     *
     * @param User $user
     * @param $photo_id
     * @internal param Photo $photo
     */
    public function __construct(User $user, $photo_id)
    {
        $this->user = $user;
        $this->photo_id = $photo_id;
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
            $dataBuilder->addData(['object' => $this->photo_id, 'type' => 'like']);
            $notificationBuilder = new PayloadNotificationBuilder();
            $notificationBuilder->setTitle('Selfy')
                ->setBody($this->user->username.' liked your photo')
                ->setSound('clean_selfy');

            $option = $optionBuiler->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();

            $downstreamResponse = FCM::sendTo($notifiable->firebase_token, $option, $notification, $data);
        }

        return [
            'user_id' => \Auth::user()->user_id,
            'photo_id' => $this->photo_id,
        ];
    }
}
