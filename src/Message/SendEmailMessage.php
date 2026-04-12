<?php
namespace App\Message;

final class SendEmailMessage
{
    public function __construct(
        public readonly string $to,// adresse e-mail du destinataire
        public readonly string $subject,// sujet de l'e-mail
        public readonly string $content,// contenu de l'e-mail
    ) {}
}