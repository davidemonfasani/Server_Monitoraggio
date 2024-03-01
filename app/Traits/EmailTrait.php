<?php

namespace App\Traits;

use Illuminate\Support\Facades\Mail;
use App\Mail\MoniError;

trait EmailTrait
{
    private function sendEmail($user, $message, string $obj)
    {
        if ($user) {
            $email = $user->email;
            Mail::to($email)->send(new MoniError($message, $obj));
        }
    }
}