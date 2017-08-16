<?php

namespace App\Tests\Manager;

use App\Entity\User;
use App\Manager\UserManager;
use App\Model\FilterUser;
use App\Pagination\InformationPagination;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class UserManagerTest extends TestCase
{
    public function testSave()
    {
        $user = new User();

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('persist')
            ->with($user)
        ;

        $manager
            ->expects($this->exactly(2))
            ->method('flush')
            ->withAnyParameters()
        ;

        $userManager = new UserManager(
            $manager,
            $this->createMock(InformationPagination::class),
            new NullLogger()
        );

        $userManager->save($user);

        $class = new \ReflectionClass($user);
        $property = $class->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($user, 1);

        $userManager->save($user);
    }

    public function testCountElements()
    {
        $filterUser = new FilterUser();

        $repository = $this->createMock(UserRepository::class);
        $repository
            ->expects($this->once())
            ->method('countByCriteria')
            ->with($filterUser)
            ->willReturn(12)
        ;

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($repository)
        ;

        $userManager = new UserManager(
            $manager,
            $this->createMock(InformationPagination::class),
            new NullLogger()
        );

        $this->assertSame(12, $userManager->countElements($filterUser));
    }

    public function testListElements()
    {
        $filterUser = new FilterUser();

        $repository = $this->createMock(UserRepository::class);
        $repository
            ->expects($this->once())
            ->method('getByCriteria')
            ->with($filterUser, ['limit' => 25, 'offset' => 0])
            ->willReturn(['element1', 'element2'])
        ;

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($repository)
        ;

        $pagination = $this->createMock(InformationPagination::class);
        $pagination
            ->expects($this->once())
            ->method('getLimitAndOffset')
            ->with(1)
            ->willReturn(['limit' => 25, 'offset' => 0])
        ;

        $userManager = new UserManager(
            $manager,
            $pagination,
            new NullLogger()
        );

        $this->assertSame(['element1', 'element2'], $userManager->listElements($filterUser, 1));
    }
}
