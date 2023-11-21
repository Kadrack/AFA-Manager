<?php
// src/EventListener/ControllerLogin.php
namespace App\EventListener;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[AsEventListener(event: 'security.authentication.success')]
class ControllerLogin
{
    private ParameterBagInterface $parameters;

    public function __construct(ParameterBagInterface $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @param AuthenticationSuccessEvent $event
     *
     * @return void
     */
    public function onSecurityAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        if ($this->parameters->get('kernel.environment') == 'dev')
        {
            $grantedUserId = array(1, 2, 3, 7, 9, 143);

            in_array($event->getAuthenticationToken()->getUser()->getId(), $grantedUserId) ?: throw new AccessDeniedException('Accès refusé');
        }
    }
}
