<?php

namespace App\Mailer;

use Psr\Log\LoggerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment as Twig;

abstract class AbstractMailer
{
    /** @var \Swift_Mailer */
    protected $mailer;

    /** @var TranslatorInterface */
    protected $translator;

    /** @var Twig */
    protected $twig;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * @param \Swift_Mailer       $mailer
     * @param TranslatorInterface $translator
     * @param Twig                $twig
     * @param LoggerInterface     $logger
     */
    public function __construct(\Swift_Mailer $mailer, TranslatorInterface $translator, Twig $twig, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->twig = $twig;
        $this->logger = $logger;
    }

    /**
     * @param string $to
     * @param string $subject
     * @param string $locale
     * @param string $body
     *
     * @return \Swift_Message
     */
    protected function buildMessage(string $to, string $subject, string $locale, string $body): \Swift_Message
    {
        return (new \Swift_Message())
            ->setFrom(Mail::SENDER)
            ->setReplyTo(Mail::REPLY_TO)
            ->setTo($to)
            ->setSubject(
                $this->translator->trans(
                    $subject,
                    [],
                    Mail::TRANSLATOR_DOMAIN,
                    $locale
                )
            )
            ->setBody($body, Mail::CONTENT_TYPE)
        ;
    }
}
