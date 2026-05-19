<?php

namespace App\Notifications\TransactionalEmails;

use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use App\Notifications\Channels\TransactionalEmailChannel;
use App\Notifications\CustomEmailNotification;
use Illuminate\Notifications\Messages\MailMessage;

class InvitationLink extends CustomEmailNotification
{
    public function via(): array
    {
        return [TransactionalEmailChannel::class];
    }

    public function __construct(public User $user, public bool $isTransactionalEmail = true)
    {
        $this->onQueue('high');
        $this->useLocale();
    }

    public function toMail(): MailMessage
    {
        $invitation = TeamInvitation::whereEmail($this->user->email)->first();
        $invitation_team = Team::find($invitation->team->id);

        return $this->withUserVisibleLocale(function () use ($invitation, $invitation_team) {
            $mail = new MailMessage;
            $mail->subject($this->trans('mail.invitation_link.subject', ['team' => $invitation_team->name]));
            $mail->view('emails.invitation-link', [
                'team' => $invitation_team->name,
                'email' => $this->user->email,
                'invitation_link' => $invitation->link,
            ]);

            return $mail;
        });
    }
}
