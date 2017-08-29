<?php

namespace App\Tests\Manager;

use App\Form\Type\FilterPictureType;
use App\Form\Type\FilterPlaceType;
use App\Form\Type\FilterUserType;
use App\Manager\FilterTypeManager;
use App\Model\FilterPicture;
use App\Model\FilterPlace;
use App\Model\FilterUser;
use App\Session\FilterStorage;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class FilterTypeManagerTest extends TestCase
{
    public function testExecuteToValidatePicturesWithFormNotSubmitted()
    {
        $request = new Request();
        $filterPicture = new FilterPicture();

        $storage = $this->createMock(FilterStorage::class);
        $storage
            ->expects($this->once())
            ->method('getFilterPicture')
            ->withAnyParameters()
            ->willReturn($filterPicture)
        ;
        $storage
            ->expects($this->never())
            ->method('saveFilterPicture')
        ;

        $form = $this->createMock(FormInterface::class);
        $form
            ->expects($this->once())
            ->method('handleRequest')
            ->with($request)
        ;
        $form
            ->expects($this->once())
            ->method('isSubmitted')
            ->withAnyParameters()
            ->willReturn(false)
        ;

        $factory = $this->createMock(FormFactoryInterface::class);
        $factory
            ->expects($this->once())
            ->method('create')
            ->with(FilterPictureType::class, $filterPicture)
            ->willReturn($form)
        ;

        $manager = new FilterTypeManager($factory, $storage);
        $this->assertSame($form, $manager->executeToValidatePictures($request));
    }

    public function testExecuteToValidatePicturesWithFormSubmitted()
    {
        $request = new Request();
        $filterPicture = new FilterPicture();

        $storage = $this->createMock(FilterStorage::class);
        $storage
            ->expects($this->once())
            ->method('getFilterPicture')
            ->withAnyParameters()
            ->willReturn($filterPicture)
        ;
        $storage
            ->expects($this->once())
            ->method('saveFilterPicture')
            ->with($filterPicture)
        ;

        $form = $this->createMock(FormInterface::class);
        $form
            ->expects($this->once())
            ->method('handleRequest')
            ->with($request)
        ;

        $form
            ->expects($this->once())
            ->method('isSubmitted')
            ->withAnyParameters()
            ->willReturn(true)
        ;

        $factory = $this->createMock(FormFactoryInterface::class);
        $factory
            ->expects($this->once())
            ->method('create')
            ->with(FilterPictureType::class, $filterPicture)
            ->willReturn($form)
        ;

        $manager = new FilterTypeManager($factory, $storage);
        $this->assertSame($form, $manager->executeToValidatePictures($request));
    }

    public function testExecuteToReValidatePicturesWithFormNotSubmitted()
    {
        $request = new Request();
        $filterPicture = new FilterPicture();

        $storage = $this->createMock(FilterStorage::class);
        $storage
            ->expects($this->once())
            ->method('getFilterPictureProcessed')
            ->withAnyParameters()
            ->willReturn($filterPicture)
        ;
        $storage
            ->expects($this->never())
            ->method('saveFilterPictureProcessed')
        ;

        $form = $this->createMock(FormInterface::class);
        $form
            ->expects($this->once())
            ->method('handleRequest')
            ->with($request)
        ;
        $form
            ->expects($this->once())
            ->method('isSubmitted')
            ->withAnyParameters()
            ->willReturn(false)
        ;

        $factory = $this->createMock(FormFactoryInterface::class);
        $factory
            ->expects($this->once())
            ->method('create')
            ->with(FilterPictureType::class, $filterPicture, ['state_processed' => true, 'username' => true])
            ->willReturn($form)
        ;

        $manager = new FilterTypeManager($factory, $storage);
        $this->assertSame($form, $manager->executeToReValidatePictures($request));
    }

    public function testExecuteToReValidatePicturesWithFormSubmitted()
    {
        $request = new Request();
        $filterPicture = new FilterPicture();

        $storage = $this->createMock(FilterStorage::class);
        $storage
            ->expects($this->once())
            ->method('getFilterPictureProcessed')
            ->withAnyParameters()
            ->willReturn($filterPicture)
        ;
        $storage
            ->expects($this->once())
            ->method('saveFilterPictureProcessed')
            ->with($filterPicture)
        ;

        $form = $this->createMock(FormInterface::class);
        $form
            ->expects($this->once())
            ->method('handleRequest')
            ->with($request)
        ;
        $form
            ->expects($this->once())
            ->method('isSubmitted')
            ->withAnyParameters()
            ->willReturn(true)
        ;

        $factory = $this->createMock(FormFactoryInterface::class);
        $factory
            ->expects($this->once())
            ->method('create')
            ->with(FilterPictureType::class, $filterPicture, ['state_processed' => true, 'username' => true])
            ->willReturn($form)
        ;

        $manager = new FilterTypeManager($factory, $storage);
        $this->assertSame($form, $manager->executeToReValidatePictures($request));
    }

    public function testExecuteToListUsersWithFormNotSubmitted()
    {
        $request = new Request();
        $filterUser = new FilterUser();

        $storage = $this->createMock(FilterStorage::class);
        $storage
            ->expects($this->once())
            ->method('getFilterUser')
            ->withAnyParameters()
            ->willReturn($filterUser)
        ;
        $storage
            ->expects($this->never())
            ->method('saveFilterUser')
        ;

        $form = $this->createMock(FormInterface::class);
        $form
            ->expects($this->once())
            ->method('handleRequest')
            ->with($request)
        ;
        $form
            ->expects($this->once())
            ->method('isSubmitted')
            ->withAnyParameters()
            ->willReturn(false)
        ;

        $factory = $this->createMock(FormFactoryInterface::class);
        $factory
            ->expects($this->once())
            ->method('create')
            ->with(FilterUserType::class, $filterUser)
            ->willReturn($form)
        ;

        $manager = new FilterTypeManager($factory, $storage);
        $this->assertSame($form, $manager->executeToListUsers($request));
    }

    public function testExecuteToListUsersWithFormSubmitted()
    {
        $request = new Request();
        $filterUser = new FilterUser();

        $storage = $this->createMock(FilterStorage::class);
        $storage
            ->expects($this->once())
            ->method('getFilterUser')
            ->withAnyParameters()
            ->willReturn($filterUser)
        ;
        $storage
            ->expects($this->once())
            ->method('saveFilterUser')
            ->with($filterUser)
        ;

        $form = $this->createMock(FormInterface::class);
        $form
            ->expects($this->once())
            ->method('handleRequest')
            ->with($request)
        ;
        $form
            ->expects($this->once())
            ->method('isSubmitted')
            ->withAnyParameters()
            ->willReturn(true)
        ;

        $factory = $this->createMock(FormFactoryInterface::class);
        $factory
            ->expects($this->once())
            ->method('create')
            ->with(FilterUserType::class, $filterUser)
            ->willReturn($form)
        ;

        $manager = new FilterTypeManager($factory, $storage);
        $this->assertSame($form, $manager->executeToListUsers($request));
    }

    public function testExecuteToListPlacesWithFormNotSubmitted()
    {
        $request = new Request();
        $filterPlace = new FilterPlace();

        $storage = $this->createMock(FilterStorage::class);
        $storage
            ->expects($this->once())
            ->method('getFilterPlace')
            ->withAnyParameters()
            ->willReturn($filterPlace)
        ;
        $storage
            ->expects($this->never())
            ->method('saveFilterPlace')
        ;

        $form = $this->createMock(FormInterface::class);
        $form
            ->expects($this->once())
            ->method('handleRequest')
            ->with($request)
        ;
        $form
            ->expects($this->once())
            ->method('isSubmitted')
            ->withAnyParameters()
            ->willReturn(false)
        ;

        $factory = $this->createMock(FormFactoryInterface::class);
        $factory
            ->expects($this->once())
            ->method('create')
            ->with(FilterPlaceType::class, $filterPlace)
            ->willReturn($form)
        ;

        $manager = new FilterTypeManager($factory, $storage);
        $this->assertSame($form, $manager->executeToListPlaces($request));
    }

    public function testExecuteToListPlacesWithFormSubmitted()
    {
        $request = new Request();
        $filterPlace = new FilterPlace();

        $storage = $this->createMock(FilterStorage::class);
        $storage
            ->expects($this->once())
            ->method('getFilterPlace')
            ->withAnyParameters()
            ->willReturn($filterPlace)
        ;
        $storage
            ->expects($this->once())
            ->method('saveFilterPlace')
            ->with($filterPlace)
        ;

        $form = $this->createMock(FormInterface::class);
        $form
            ->expects($this->once())
            ->method('handleRequest')
            ->with($request)
        ;
        $form
            ->expects($this->once())
            ->method('isSubmitted')
            ->withAnyParameters()
            ->willReturn(true)
        ;

        $factory = $this->createMock(FormFactoryInterface::class);
        $factory
            ->expects($this->once())
            ->method('create')
            ->with(FilterPlaceType::class, $filterPlace)
            ->willReturn($form)
        ;

        $manager = new FilterTypeManager($factory, $storage);
        $this->assertSame($form, $manager->executeToListPlaces($request));
    }
}
