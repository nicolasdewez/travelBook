<?php

namespace App\Tests\Manager;

use App\Entity\Place;
use App\Manager\PlaceManager;
use App\Model\FilterPlace;
use App\Pagination\InformationPagination;
use App\Repository\PlaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class PlaceManagerTest extends TestCase
{
    public function testCountElements()
    {
        $filterPlace = new FilterPlace();

        $repository = $this->createMock(PlaceRepository::class);
        $repository
            ->expects($this->once())
            ->method('countByCriteria')
            ->with($filterPlace)
            ->willReturn(12)
        ;

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->with(Place::class)
            ->willReturn($repository)
        ;

        $placeManager = new PlaceManager(
            $manager,
            $this->createMock(InformationPagination::class),
            new NullLogger()
        );

        $this->assertSame(12, $placeManager->countElements($filterPlace));
    }

    public function testListElements()
    {
        $filterPlace = new FilterPlace();

        $repository = $this->createMock(PlaceRepository::class);
        $repository
            ->expects($this->once())
            ->method('getByCriteria')
            ->with($filterPlace, ['limit' => 25, 'offset' => 0])
            ->willReturn(['element1', 'element2'])
        ;

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->with(Place::class)
            ->willReturn($repository)
        ;

        $pagination = $this->createMock(InformationPagination::class);
        $pagination
            ->expects($this->once())
            ->method('getLimitAndOffset')
            ->with(1)
            ->willReturn(['limit' => 25, 'offset' => 0])
        ;

        $placeManager = new PlaceManager(
            $manager,
            $pagination,
            new NullLogger()
        );

        $this->assertSame(['element1', 'element2'], $placeManager->listElements($filterPlace, 1));
    }
}
