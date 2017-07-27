<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\SecurityEvents;

class UserLocaleSubscriber implements EventSubscriberInterface
{
    /** @var SessionInterface */
    private $session;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param SessionInterface $session
     * @param LoggerInterface  $logger
     */
    public function __construct(SessionInterface $session, LoggerInterface $logger)
    {
        $this->session = $session;
        $this->logger = $logger;
    }

    /**
     * @param InteractiveLoginEvent $event
     */
    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        if (!($user instanceof User)) {
            $this->logger->error(sprintf(
                'User %s is not a valid user (User instance expected, %s found)',
                $user->getUsername(),
                get_class($user)
            ));

            return;
        }

        if (null !== $user->getLocale()) {
            $this->session->set('_locale', $user->getLocale());
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => [['onInteractiveLogin']],
        ];
    }
}
