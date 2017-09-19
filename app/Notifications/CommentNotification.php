<?php

namespace App\Notifications;

use App\Helpers\WindowsPhone;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
class CommentNotification extends Notification
{
    use Queueable;

    private $user;
    private $photo_id;
    private $comment_id;

    /**
     * Create a new notification instance.
     *
     * @param User $user
     * @param $photo_id
     * @param $comment_id
     * @internal param Photo $photo
     */
    public function __construct(User $user, $photo_id, $comment_id)
    {
        $this->user = $user;
        $this->photo_id = $photo_id;
        $this->comment_id = $comment_id;
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

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        if($notifiable->firebase_token != null)
        {
            $optionBuiler = new OptionsBuilder();
            $optionBuiler->setTimeToLive(60*20)->setPriority("high");

            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData(['object' => $this->photo_id, 'type' => 'comment']);
            $notificationBuilder = new PayloadNotificationBuilder();
            $notificationBuilder->setTitle('Selfy')
                ->setBody($this->user->username.' commented on your photo')
                ->setSound('clean_selfy');

            $option = $optionBuiler->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();

           FCM::sendTo($notifiable->firebase_token, $option, $notification, $data);
        }

        else if($notifiable->wp_token != null)
        {
            $windowsPhone = new WindowsPhone($notifiable->wp_token);
            $windowsPhone->push_toast($this->photo_id, "Photo", "Selfy", $this->user->username.' commented on your photo');
        }

        return [
            'user_id' => \Auth::user()->user_id,
            'comment_id' => $this->comment_id,
            'photo_id' => $this->photo_id,
        ];
    }
}
