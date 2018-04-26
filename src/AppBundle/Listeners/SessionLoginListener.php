<?php

namespace AppBundle\Listeners;


use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;


class SessionLoginListener
{



    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();


        if ($user->getActivo()!=1) {
            throw new AuthenticationException("Su usuario se encuentra bloqueado.", NULL, NULL);
        }

    }

}
