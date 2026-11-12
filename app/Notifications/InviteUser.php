<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Invitation email for a newly created user. Uses the password-reset token flow,
 * so the link opens the normal "set password" page.
 */
class InviteUser extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $minutes = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

        return (new MailMessage)
            ->subject("You're invited to the Crystal Creek Estimating Desk")
            ->greeting("Hello {$notifiable->name},")
            ->line('An account has been created for you on the Crystal Creek Estimating Desk.')
            ->action('Set your password', $this->resetUrl($notifiable))
            ->line("This link expires in {$minutes} minutes. If it expires, ask your manager to resend the invitation.");
    }
}
