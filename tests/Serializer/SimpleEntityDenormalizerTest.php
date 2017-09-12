<?php

namespace App\Tests\Serializer;

use App\Entity\User;
use App\Serializer\SimpleEntityDenormalizer;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class SimpleEntityDenormalizerTest extends TestCase
{
    public function testSupportsDenormalization()
    {
        $simpleEntityDenormalizer = new SimpleEntityDenormalizer($this->createMock(EntityManagerInterface::class));

        $this->assertTrue($simpleEntityDenormalizer->supportsDenormalization(['id' => 1], User::class));
        $this->assertFalse($simpleEntityDenormalizer->supportsDenormalization([], User::class));
        $this->assertFalse($simpleEntityDenormalizer->supportsDenormalization(['id' => 1], SimpleEntityDenormalizer::class));
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

        $simpleEntityDenormalizer = new SimpleEntityDenormalizer($manager);
        $this->assertSame($user, $simpleEntityDenormalizer->denormalize(['id' => 1], User::class));
    }
}
