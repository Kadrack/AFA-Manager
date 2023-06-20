<?php
// src/Service/UserTools.php
namespace App\Notifier;

use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;

use Symfony\Component\Security\Http\LoginLink\LoginLinkNotification;

/**
 *
 */
class CustomLoginLinkNotification extends LoginLinkNotification
{
    /**
     * @param EmailRecipientInterface $recipient
     * @param string|null $transport
     * @return EmailMessage|null
     */
    public function asEmailMessage(EmailRecipientInterface $recipient, string $transport = null): ?EmailMessage
    {
        $emailMessage = parent::asEmailMessage($recipient, $transport);

        // get the NotificationEmail object and override the template
        $email = $emailMessage->getMessage();

        if ($transport == 'AFA-Manager - Récupération mot de passe')
        {
            $email->htmlTemplate('Mails/loginLinkMail.html.twig');
        }
        else
        {
            $email->htmlTemplate('Mails/loginCreate.html.twig');
        }

        return $emailMessage;
    }
}
