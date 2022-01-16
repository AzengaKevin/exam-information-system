<?php

namespace App\Actions\Messages;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendAdvantaSMS
{
    
    /** 
     * Call the API to send the notification
     */
    public static function invoke(array $payload) : bool
    {
        $baseUrl = Constants::ADVANTA_BASE_URL;

        try {

            $response = Http::baseUrl($baseUrl)
                ->post("services/sendsms/", [
                    "apikey" => config('services.advanta.apikey'),
                    "partnerID" => config('services.advanta.partnerID'),
                    "message" => $payload['content'],
                    "shortcode" => config('services.advanta.shortcode'),
                    "mobile" => $payload['phone']
                ]);

            //Log::debug($response->json());

            return $response->ok();
            
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            return false;
        }
        
    }
}
