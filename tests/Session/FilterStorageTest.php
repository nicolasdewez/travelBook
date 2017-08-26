<?php

namespace App\Tests\Session;

use App\Model\FilterPicture;
use App\Model\FilterUser;
use App\Session\FilterStorage;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FilterStorageTest extends TestCase
{
    public function testSaveFilterUser()
    {
        $filterUser = new FilterUser();

        $session = $this->createMock(SessionInterface::class);
        $session
            ->expects($this->once())
            ->method('set')
            ->with(FilterStorage::FILTER_USER, $filterUser)
        ;

        $storage = new FilterStorage($session);
        $storage->saveFilterUser($filterUser);
    }

    public function testGetFilterUser()
    {
        $filterUser = new FilterUser();

        $session = $this->createMock(SessionInterface::class);
        $session
            ->expects($this->once())
            ->method('get')
            ->with(FilterStorage::FILTER_USER, new FilterUser())
            ->willReturn($filterUser)
        ;

        $storage = new FilterStorage($session);
        $this->assertSame($filterUser, $storage->getFilterUser());
    }

    public function testSaveFilterPicture()
    {
        $filterPicture = new FilterPicture();

        $session = $this->createMock(SessionInterface::class);
        $session
            ->expects($this->once())
            ->method('set')
            ->with(FilterStorage::FILTER_PICTURE, $filterPicture)
        ;

        $storage = new FilterStorage($session);
        $storage->saveFilterPicture($filterPicture);
    }

    public function testGetFilterPicture()
    {
        $filterPicture = new FilterPicture();

        $session = $this->createMock(SessionInterface::class);
        $session
            ->expects($this->once())
            ->method('get')
            ->with(FilterStorage::FILTER_PICTURE, new FilterPicture())
            ->willReturn($filterPicture)
        ;

        $storage = new FilterStorage($session);
        $this->assertSame($filterPicture, $storage->getFilterPicture());
    }

    public function testSaveFilterPictureProcessed()
    {
        $filterPicture = new FilterPicture();

        $session = $this->createMock(SessionInterface::class);
        $session
            ->expects($this->once())
            ->method('set')
            ->with(FilterStorage::FILTER_PICTURE_PROCESSED, $filterPicture)
        ;

        $storage = new FilterStorage($session);
        $storage->saveFilterPictureProcessed($filterPicture);
    }

    public function testGetFilterPictureProcessed()
    {
        $filterPicture = new FilterPicture();

        $session = $this->createMock(SessionInterface::class);
        $session
            ->expects($this->once())
            ->method('get')
            ->with(FilterStorage::FILTER_PICTURE_PROCESSED, new FilterPicture())
            ->willReturn($filterPicture)
        ;

        $storage = new FilterStorage($session);
        $this->assertSame($filterPicture, $storage->getFilterPictureProcessed());
    }
}
