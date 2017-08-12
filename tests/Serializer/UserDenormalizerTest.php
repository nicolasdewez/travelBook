<?php

namespace App\Tests\Serializer;

use App\Entity\User;
use App\Serializer\UserDenormalizer;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class UserDenormalizerTest extends TestCase
{
    public function testSupportsDenormalization()
    {
        $userDenormalizer = new UserDenormalizer($this->createMock(EntityManagerInterface::class));

        $this->assertTrue($userDenormalizer->supportsDenormalization(['id' => 1], User::class));
        $this->assertFalse($userDenormalizer->supportsDenormalization([], User::class));
        $this->assertFalse($userDenormalizer->supportsDenormalization(['id' => 1], UserDenormalizer::class));
    }

    public function testDenormalize()
    {
        $user = new User();

        $repository = $this->createMock(ObjectRepository::class);
        $repository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($user)
        ;

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($repository)
        ;

        $userDenormalizer = new UserDenormalizer($manager);
        $this->assertSame($user, $userDenormalizer->denormalize(['id' => 1], ''));
    }
}
