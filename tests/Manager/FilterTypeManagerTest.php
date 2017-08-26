<?php

namespace App\Tests\Manager;

use App\Form\Type\FilterPictureType;
use App\Manager\FilterTypeManager;
use App\Model\FilterPicture;
use App\Session\FilterStorage;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class FilterTypeManagerTest extends TestCase
{
    public function testExecuteToValidateWithFormNotSubmitted()
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
        $this->assertSame($form, $manager->executeToValidate($request));
    }

    public function testExecuteToValidateWithFormSubmitted()
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
        $this->assertSame($form, $manager->executeToValidate($request));
    }

    public function testExecuteToReValidateWithFormNotSubmitted()
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
        $this->assertSame($form, $manager->executeToRevalidate($request));
    }

    public function testExecuteToReValidateWithFormSubmitted()
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
        $this->assertSame($form, $manager->executeToRevalidate($request));
    }
}
