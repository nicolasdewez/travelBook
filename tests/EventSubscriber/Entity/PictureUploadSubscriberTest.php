<?php

namespace App\Tests\EventSubscriber\Entity;

use App\Entity\Picture;
use App\Entity\User;
use App\EventSubscriber\Entity\PictureUploadSubscriber;
use App\Uploader\PictureUploader;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureUploadSubscriberTest extends TestCase
{
    public function testPrePersist()
    {
        $file = new UploadedFile(__FILE__, 'name');

        $uploader = $this->createMock(PictureUploader::class);
        $uploader
            ->expects($this->once())
            ->method('execute')
            ->with($file)
            ->willReturn('filename')
        ;

        $subscriber = new PictureUploadSubscriber($uploader);

        $picture = (new Picture())->setFile($file);

        $life = new LifecycleEventArgs(
            $picture,
            $this->createMock(ObjectManager::class)
        );

        $subscriber->prePersist($life);

        $this->assertSame('filename', $picture->getName());
    }

    public function testPrePersistNotFile()
    {
        $uploader = $this->createMock(PictureUploader::class);
        $uploader
            ->expects($this->never())
            ->method('execute')
        ;

        $subscriber = new PictureUploadSubscriber($uploader);

        $picture = new Picture();

        $life = new LifecycleEventArgs(
            $picture,
            $this->createMock(ObjectManager::class)
        );

        $subscriber->prePersist($life);

        $this->assertNull($picture->getName());
    }

    public function testPrePersistNotSupported()
    {
        $uploader = $this->createMock(PictureUploader::class);
        $uploader
            ->expects($this->never())
            ->method('execute')
        ;

        $subscriber = new PictureUploadSubscriber($uploader);

        $user = new User();

        $life = new LifecycleEventArgs(
            $user,
            $this->createMock(ObjectManager::class)
        );

        $subscriber->prePersist($life);
    }

    public function testSupports()
    {
        $subscriber = new PictureUploadSubscriber($this->createMock(PictureUploader::class));
        $class = new \ReflectionClass($subscriber);
        $method = $class->getMethod('supports');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($subscriber, [new Picture()]));
        $this->assertFalse($method->invokeArgs($subscriber, [new User()]));
    }

    public function testGetSubscribedEvents()
    {
        $subscriber = new PictureUploadSubscriber($this->createMock(PictureUploader::class));
        $this->assertSame(['prePersist'], $subscriber->getSubscribedEvents());
    }
}
