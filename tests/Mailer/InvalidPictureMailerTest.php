<?php

namespace App\Tests\Mailer;

use App\Entity\InvalidationPicture;
use App\Entity\Picture;
use App\Entity\Travel;
use App\Entity\User;
use App\Mailer\InvalidPictureMailer;
use App\Mailer\Mail;
use App\Translation\Locale;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment as Twig;

class InvalidPictureMailerTest extends TestCase
{
    public function testExecute()
    {
        $user = (new User())
            ->setLocale(Locale::FR)
            ->setEmail('email@example.com')
        ;

        $invalidationPicture = (new InvalidationPicture())
            ->setPicture((new Picture())->setTravel((new Travel())->setUser($user)))
        ;

        $swiftMailer = $this->createMock(\Swift_Mailer::class);
        $swiftMailer
            ->expects($this->once())
            ->method('send')
        ;

        $translator = $this->createMock(TranslatorInterface::class);
        $translator
            ->expects($this->once())
            ->method('trans')
            ->with(Mail::SUBJECT_PICTURE_INVALID, [], Mail::TRANSLATOR_DOMAIN, Locale::FR)
            ->willReturn('subject')
        ;

        $twig = $this->createMock(Twig::class);
        $twig
            ->expects($this->once())
            ->method('render')
            ->with('mailing/invalid-picture.html.twig', ['invalidationPicture' => $invalidationPicture])
            ->willReturn('body')
        ;

        $mailer = new InvalidPictureMailer(
            $swiftMailer,
            $translator,
            $twig,
            new NullLogger()
        );

        $mailer->execute($invalidationPicture);
    }
}
