<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends BaseVerifyEmail
{
    /**
     * Build the email verification URL pointing to the SPA frontend.
     *
     * The frontend will receive the signed API URL parameters and forward
     * the request to /api/email/verify/{id}/{hash} automatically.
     */
    protected function verificationUrl(mixed $notifiable): string
    {
        // Build the signed API URL
        $signedApiUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id'   => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        // Extract the query string (expires, signature) from the signed API URL
        $parsed = parse_url($signedApiUrl);
        parse_str($parsed['query'] ?? '', $queryParams);

        // Build the frontend URL: /email/verify/{id}/{hash}?expires=...&signature=...
        $frontendBase = rtrim(config('app.frontend_url', config('app.url')), '/');
        $id           = $notifiable->getKey();
        $hash         = sha1($notifiable->getEmailForVerification());

        return $frontendBase
            . '/email/verify/' . $id . '/' . $hash
            . '?' . http_build_query($queryParams);
    }
}

