<?php

namespace App\Tests\Security;

use App\Entity\User;
use App\Producer\MailUpdateAccountProducer;
use App\Security\RefreshToken;
use App\Security\UpdateAccount;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UpdateAccountTest extends TestCase
{
    public function testExecuteWithoutNewPassword()
    {
        $user = new User();

        $encoder = $this->createMock(UserPasswordEncoderInterface::class);
        $encoder
            ->expects($this->never())
            ->method('encodePassword')
        ;

        $updateAccount = $this->getUpdateAccount($encoder, $user);

        $updateAccount->execute($user);
    }

    public function testExecuteWithNewPassword()
    {
        $user = (new User())
            ->setNewPassword('new')
        ;

        $encoder = $this->createMock(UserPasswordEncoderInterface::class);
        $encoder
            ->expects($this->once())
            ->method('encodePassword')
            ->with($user, 'new')
            ->willReturn('encoded')
        ;

        $updateAccount = $this->getUpdateAccount($encoder, $user);

        $updateAccount->execute($user);

        $this->assertSame('encoded', $user->getPassword());
    }

    public function testExecuteWithNewPasswordAndWithoutMail()
    {
        $user = (new User())
            ->setNewPassword('new')
            ->setEmailNotification(false)
        ;

        $encoder = $this->createMock(UserPasswordEncoderInterface::class);
        $encoder
            ->expects($this->once())
            ->method('encodePassword')
            ->with($user, 'new')
            ->willReturn('encoded')
        ;

        $updateAccount = $this->getUpdateAccount($encoder, $user, 0);

        $updateAccount->execute($user);

        $this->assertSame('encoded', $user->getPassword());
    }

    /**
     * @param User                         $user
     * @param UserPasswordEncoderInterface $encoder
     * @param int                          $countProducer
     *
     * @return UpdateAccount
     */
    private function getUpdateAccount(UserPasswordEncoderInterface $encoder, User $user, int $countProducer = 1)
    {
        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('flush')
            ->withAnyParameters()
        ;

        $refresh = $this->createMock(RefreshToken::class);
        $refresh
            ->expects($this->once())
            ->method('execute')
            ->with($user)
        ;

        $producer = $this->createMock(MailUpdateAccountProducer::class);
        $producer
            ->expects($this->exactly($countProducer))
            ->method('execute')
            ->with($user)
        ;

        return new UpdateAccount(
            $manager,
            $encoder,
            $refresh,
            $producer,
            new NullLogger()
        );
    }
}
