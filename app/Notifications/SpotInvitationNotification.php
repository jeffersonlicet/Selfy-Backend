<?php

namespace App\Notifications;

use App\Models\Photo;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;



class SpotInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $user;
    private $challenge_id;
    private $place_name;

    /**
     * Create a new notification instance.
     *
     * @param User $user
     * @param $challenge_id

    ]
     * @param $place_name
     */
    public function __construct(User $user, $challenge_id, $place_name)
    {
        $this->user = $user;
        $this->challenge_id = $challenge_id;
        $this->place_name = $place_name;
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
            $dataBuilder->addData(['object' => $this->user->user_id, 'type' => 'spot_invitation']);
            $notificationBuilder = new PayloadNotificationBuilder();

            $notificationBuilder->setTitle('New Challenge')
                ->setBody('Take a selfie in '.$this->place_name)
                ->setSound('clean_selfy');

            $option         = $optionBuiler->build();
            $notification   = $notificationBuilder->build();
            $data           = $dataBuilder->build();

            FCM::sendTo($notifiable->firebase_token, $option, $notification, $data);
        }

        return [
            'user_id' => $this->user->user_id,
            'challenge_id' => $this->challenge_id
        ];
    }
}
