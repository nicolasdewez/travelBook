<?php

namespace App\Tests\Mailer;

use App\Entity\User;
use App\Mailer\Mail;
use App\Mailer\PasswordLostMailer;
use App\Translation\Locale;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment as Twig;

class PasswordLostMailerTest extends TestCase
{
    public function testExecute()
    {
        $user = (new User())
            ->setLocale(Locale::FR)
            ->setEmail('email@example.com')
        ;

        $swiftMailer = $this->createMock(\Swift_Mailer::class);
        $swiftMailer->expects($this->once())->method('send');

        $translator = $this->createMock(TranslatorInterface::class);
        $translator
            ->expects($this->once())
            ->method('trans')
            ->with(Mail::SUBJECT_PASSWORD_LOST, [], Mail::TRANSLATOR_DOMAIN, Locale::FR)
            ->willReturn('subject')
        ;

        $twig = $this->createMock(Twig::class);
        $twig
            ->expects($this->once())
            ->method('render')
            ->with('mailing/password-lost.html.twig', ['user' => $user, 'password' => 'password'])
            ->willReturn('body')
        ;

        $mailer = new PasswordLostMailer(
            $swiftMailer,
            $translator,
            $twig,
            new NullLogger()
        );

        $mailer->execute($user, 'password');
    }
}
