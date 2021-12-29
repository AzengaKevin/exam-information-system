<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;

class AdvantaChannel
{
    
    /**
     * Call the action to send the adavanta SMS
     * 
     * @return void|bool
     */
    public function send($notifiable, Notification $notification)
    {
        if (method_exists($notifiable, 'routeNotificationForAdvanta')) {
            $id = $notifiable->routeNotificationForAdvanta($notifiable);
        } else {
            $id = $notifiable->getKey();
        }

        $data = method_exists($notification, 'toAdvanta')
            ? $notification->toAdvanta($notifiable)
            : $notification->toArray($notifiable);

        if (empty($data)) {
            return;
        }

        // Call the action to send message via api here
        app('log')->info(json_encode([
            'id'   => $id,
            'data' => $data,
        ]));

        return true;
    }
}
