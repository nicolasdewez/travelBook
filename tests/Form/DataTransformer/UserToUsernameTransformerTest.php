<?php

namespace App\Tests\Form\DataTransformer;

use App\Entity\User;
use App\Form\DataTransformer\UserToUsernameTransformer;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class UserToUsernameTransformerTest extends TestCase
{
    public function testTransformWithUserNull()
    {
        $transformer = new UserToUsernameTransformer($this->createMock(EntityManagerInterface::class));

        $this->assertSame('', $transformer->transform(null));
    }

    public function testTransform()
    {
        $transformer = new UserToUsernameTransformer($this->createMock(EntityManagerInterface::class));

        $user = (new User())->setUsername('username');

        $this->assertSame('username', $transformer->transform($user));
    }

    public function testReverseTransformNotFound()
    {
        $repository = $this->createMock(ObjectRepository::class);
        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['username' => 'username'])
            ->willReturn(null)
        ;

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($repository)
        ;

        $transformer =  new UserToUsernameTransformer($manager);
        $this->assertNull($transformer->reverseTransform('username'));
    }

    public function testReverseTransform()
    {
        $user = new User();

        $repository = $this->createMock(ObjectRepository::class);
        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['username' => 'username'])
            ->willReturn($user)
        ;

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($repository)
        ;

        $transformer =  new UserToUsernameTransformer($manager);
        $this->assertSame($user, $transformer->reverseTransform('username'));
    }
}
