<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendPasswordNotification extends Notification
{
    use Queueable;

    public string $password;

    /**
     * Create a new notification instance.
     * 
     * @param string $password
     *
     * @return void
     */
    public function __construct(string $password)
    {
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $viaArray = array('advanta');

        if(!empty($$notifiable->email)) array_push($viaArray, "mail");
        
        return $viaArray;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)->line("Hello {$notifiable->name}, your login password is, {$this->password}. Change it as soon as you login");
    }

    /**
     * Get the data of the message details
     * 
     * @param  mixed  $notifiable
     * 
     * @return array
     */
    public function toAdvanta($notifiable)
    {
        return [
            'content' => "Hello {$notifiable->name}, your login password is, {$this->password}. Change it as soon as you login"
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
