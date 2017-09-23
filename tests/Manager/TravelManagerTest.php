<?php

namespace App\Tests\Manager;

use App\Entity\Travel;
use App\Entity\User;
use App\Manager\TravelManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class TravelManagerTest extends TestCase
{
    public function testSave()
    {
        $travel = new Travel();

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('persist')
            ->with($travel)
        ;

        $manager
            ->expects($this->exactly(2))
            ->method('flush')
            ->withAnyParameters()
        ;

        $travelManager = new TravelManager($manager, new NullLogger());

        $travelManager->save($travel);

        $class = new \ReflectionClass($travel);
        $property = $class->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($travel, 1);

        $travelManager->save($travel);
    }

    public function testListByUser()
    {
        $travels = [new Travel(), new Travel()];
        $user = new User();

        $repository = $this->createMock(ObjectRepository::class);
        $repository
            ->expects($this->once())
            ->method('findBy')
            ->with(['user' => $user], ['id' => 'ASC'])
            ->willReturn($travels)
        ;

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->with(Travel::class)
            ->willReturn($repository)
        ;

        $travelManager = new TravelManager($manager, new NullLogger());

        $this->assertSame($travels, $travelManager->listByUser($user));
    }
}
