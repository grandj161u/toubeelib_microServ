<?php

namespace app_consumer\core\services\mails;

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

class ServiceMail implements ServiceMailInterface
{
    private Mailer $mailer;
    private string $from;

    public function __construct(Mailer $mailer, string $from)
    {
        $this->mailer = $mailer;
        $this->from = $from;
    }

    public function sendMail(string $to, string $cc, string $subject, string $content): void
    {
        $email = (new Email())
            ->from($this->from)
            ->to($to)
            ->cc($cc)
            ->subject($subject)
            ->html($content);

        $this->mailer->send($email);
    }

    public function notifyRdvCreated(array $rdvData): void
    {
        $content = $this->buildRdvCreatedContent($rdvData);
        $this->sendMail(
            'patient@example.com',
            'praticien@example.com',
            'Notification de rendez-vous',
            $content
        );
    }

    public function notifyRdvCanceled(array $rdvData): void
    {
        $content = $this->buildRdvCanceledContent($rdvData);
        $this->sendMail(
            'patient@example.com',
            'praticien@example.com',
            'Notification de rendez-vous',
            $content
        );
    }

    private function buildRdvCreatedContent(array $rdvData): string
    {
        return "<p>Un nouveau rendez-vous a été créé :</p>
                <p>ID du RDV : {$rdvData['IdRdv']}</p>
                <p>ID du Praticien : {$rdvData['IdPraticien']}</p>
                <p>ID du Patient : {$rdvData['IdPatient']}</p>
                <p>Le {$rdvData['Horaire']['date']}</p>";
    }

    private function buildRdvCanceledContent(array $rdvData): string
    {
        return "<p>Un rendez-vous a été supprimé :</p>
                <p>ID du RDV : {$rdvData['IdRdv']}</p>
                <p>ID du Praticien : {$rdvData['IdPraticien']}</p>
                <p>ID du Patient : {$rdvData['IdPatient']}</p>
                <p>Le {$rdvData['Horaire']['date']}</p>";
    }
}
