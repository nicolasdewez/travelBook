<?php

namespace App\Tests\Manager;

use App\Entity\Picture;
use App\Entity\User;
use App\Manager\PictureManager;
use App\Model\FilterPicture;
use App\Pagination\InformationPagination;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class PictureManagerTest extends TestCase
{
    public function testSave()
    {
        $picture = new Picture();

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('persist')
            ->with($picture)
        ;

        $manager
            ->expects($this->exactly(2))
            ->method('flush')
            ->withAnyParameters()
        ;

        $pictureManager = new PictureManager(
            $manager,
            $this->createMock(InformationPagination::class),
            new NullLogger()
        );

        $pictureManager->save($picture);

        $class = new \ReflectionClass($picture);
        $property = $class->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($picture, 1);

        $pictureManager->save($picture);
    }

    public function testCountToValidationElements()
    {
        $filterPicture = new FilterPicture();

        $repository = $this->createMock(PictureRepository::class);
        $repository
            ->expects($this->once())
            ->method('countToValidationByCriteria')
            ->with($filterPicture)
            ->willReturn(12)
        ;

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->with(Picture::class)
            ->willReturn($repository)
        ;

        $pictureManager = new PictureManager(
            $manager,
            $this->createMock(InformationPagination::class),
            new NullLogger()
        );

        $this->assertSame(12, $pictureManager->countToValidationElements($filterPicture));
    }

    public function testCountToReValidationElements()
    {
        $filterPicture = new FilterPicture();

        $repository = $this->createMock(PictureRepository::class);
        $repository
            ->expects($this->once())
            ->method('countToReValidationByCriteria')
            ->with($filterPicture)
            ->willReturn(12)
        ;

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->with(Picture::class)
            ->willReturn($repository)
        ;

        $pictureManager = new PictureManager(
            $manager,
            $this->createMock(InformationPagination::class),
            new NullLogger()
        );

        $this->assertSame(12, $pictureManager->countToReValidationElements($filterPicture));
    }

    public function testListToValidationElements()
    {
        $filterPicture = new FilterPicture();

        $repository = $this->createMock(PictureRepository::class);
        $repository
            ->expects($this->once())
            ->method('getToValidationByCriteria')
            ->with($filterPicture, ['limit' => 25, 'offset' => 0])
            ->willReturn(['element1', 'element2'])
        ;

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->with(Picture::class)
            ->willReturn($repository)
        ;

        $pagination = $this->createMock(InformationPagination::class);
        $pagination
            ->expects($this->once())
            ->method('getLimitAndOffset')
            ->with(1)
            ->willReturn(['limit' => 25, 'offset' => 0])
        ;

        $pictureManager = new PictureManager(
            $manager,
            $pagination,
            new NullLogger()
        );

        $this->assertSame(['element1', 'element2'], $pictureManager->listToValidationElements($filterPicture, 1));
    }

    public function testListToReValidationElements()
    {
        $filterPicture = new FilterPicture();

        $repository = $this->createMock(PictureRepository::class);
        $repository
            ->expects($this->once())
            ->method('getToReValidationByCriteria')
            ->with($filterPicture, ['limit' => 25, 'offset' => 0])
            ->willReturn(['element1', 'element2'])
        ;

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->with(Picture::class)
            ->willReturn($repository)
        ;

        $pagination = $this->createMock(InformationPagination::class);
        $pagination
            ->expects($this->once())
            ->method('getLimitAndOffset')
            ->with(1)
            ->willReturn(['limit' => 25, 'offset' => 0])
        ;

        $pictureManager = new PictureManager(
            $manager,
            $pagination,
            new NullLogger()
        );

        $this->assertSame(['element1', 'element2'], $pictureManager->listToReValidationElements($filterPicture, 1));
    }

    public function testListByUser()
    {
        $user = new  User();

        $repository = $this->createMock(PictureRepository::class);
        $repository
            ->expects($this->once())
            ->method('getByUser')
            ->with($user)
            ->willReturn(['element1', 'element2'])
        ;

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->with(Picture::class)
            ->willReturn($repository)
        ;

        $pictureManager = new PictureManager(
            $manager,
            $this->createMock(InformationPagination::class),
            new NullLogger()
        );

        $this->assertSame(['element1', 'element2'], $pictureManager->listByUser($user));
    }
}
