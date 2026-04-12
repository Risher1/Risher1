<?php
namespace App\MessageHandler;

use App\Message\SendEmailMessage;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
final class SendEmailMessageHandler
{
    public function __construct(
        private MailerInterface $mailer,
    ) {}

    public function __invoke(SendEmailMessage $message): void
    {
        $email = (new Email())
            ->from('no-reply@tonsite.fr')
            ->to($message->to)
            ->subject($message->subject)
            ->text($message->content);

        $this->mailer->send($email);
    }
}