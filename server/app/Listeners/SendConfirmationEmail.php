<?php

namespace App\Listeners;

use App\Events\UserCreated;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;

class SendConfirmationEmail
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\UserCreated  $event
     * @return void
     */
    public function handle(UserCreated $event)
    {
        $linkHash = urlencode(Crypt::encrypt($event->user->email));
        $verificationLink = config('app.client_url') . "/users/confirmation/{$linkHash}";

        $emailBody = "
            <p>Welcome, {$event->user->name}. Thanks for trying out my demo application!</p>
            <p>You must verify your account before using it, please <a href='{$verificationLink}'>click here</a></p>
            <hr>
            <p><a href='https://github.com/cedaesca/todolist'>Click here</a> to check out the source code</p>
        ";

        Mail::html($emailBody, function ($message) use ($event) {
            $message->subject('You must verify your account');
            $message->to($event->user->email);
        });
    }
}
