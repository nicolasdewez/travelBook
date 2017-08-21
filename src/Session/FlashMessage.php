<?php

namespace App\Session;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;

class FlashMessage
{
    /** @var SessionInterface */
    private $session;

    /** @var TranslatorInterface */
    private $translator;

    /**
     * @param SessionInterface    $session
     * @param TranslatorInterface $translator
     */
    public function __construct(SessionInterface $session, TranslatorInterface $translator)
    {
        $this->session = $session;
        $this->translator = $translator;
    }

    /**
     * @param string     $type
     * @param string     $message
     * @param array|null $parameters
     */
    public function add(string $type, string $message, array $parameters = null)
    {
        if (null !== $parameters) {
            $message = $this->translator->trans($message, $parameters);
        }

        $this->session->getFlashBag()->add($type, $message);
    }
}
