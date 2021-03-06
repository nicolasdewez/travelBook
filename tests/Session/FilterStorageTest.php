<?php

namespace App\Tests\Session;

use App\Model\FilterFeedback;
use App\Model\FilterPicture;
use App\Model\FilterPlace;
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

    public function testSaveFilterPlace()
    {
        $filterPlace = new FilterPlace();

        $session = $this->createMock(SessionInterface::class);
        $session
            ->expects($this->once())
            ->method('set')
            ->with(FilterStorage::FILTER_PLACE, $filterPlace)
        ;

        $storage = new FilterStorage($session);
        $storage->saveFilterPlace($filterPlace);
    }

    public function testGetFilterPlace()
    {
        $filterPlace = new FilterPlace();

        $session = $this->createMock(SessionInterface::class);
        $session
            ->expects($this->once())
            ->method('get')
            ->with(FilterStorage::FILTER_PLACE, new FilterPlace())
            ->willReturn($filterPlace)
        ;

        $storage = new FilterStorage($session);
        $this->assertSame($filterPlace, $storage->getFilterPlace());
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
    
    public function testSaveFilterFeedback()
    {
        $filterFeedback = new FilterFeedback();

        $session = $this->createMock(SessionInterface::class);
        $session
            ->expects($this->once())
            ->method('set')
            ->with(FilterStorage::FILTER_FEEDBACK, $filterFeedback)
        ;

        $storage = new FilterStorage($session);
        $storage->saveFilterFeedback($filterFeedback);
    }

    public function testGetFilterFeedback()
    {
        $filterFeedback = new FilterFeedback();

        $session = $this->createMock(SessionInterface::class);
        $session
            ->expects($this->once())
            ->method('get')
            ->with(FilterStorage::FILTER_FEEDBACK, new FilterFeedback())
            ->willReturn($filterFeedback)
        ;

        $storage = new FilterStorage($session);
        $this->assertSame($filterFeedback, $storage->getFilterFeedback());
    }
}
