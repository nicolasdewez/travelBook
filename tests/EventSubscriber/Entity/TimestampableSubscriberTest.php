<?php

namespace App\Tests\EventSubscriber\Entity;

use App\Entity\Timestampable;
use App\Entity\User;
use App\EventSubscriber\Entity\TimestampableSubscriber;
use App\Model\FilterPlace;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use PHPUnit\Framework\TestCase;

class TimestampableSubscriberTest extends TestCase
{
    public function testPrePersist()
    {
        $subscriber = new TimestampableSubscriber();

        $user = new User();

        $life = new LifecycleEventArgs(
            $user,
            $this->createMock(ObjectManager::class)
        );

        $subscriber->prePersist($life);

        $this->assertNotNull($user->getCreatedAt());
        $this->assertNotNull($user->getUpdatedAt());
    }

    public function testPreUpdate()
    {
        $subscriber = new TimestampableSubscriber();

        $user = new User();

        $life = new LifecycleEventArgs(
            $user,
            $this->createMock(ObjectManager::class)
        );

        $subscriber->preUpdate($life);

        $this->assertNotNull($user->getUpdatedAt());
    }

    public function testSaveUpdatedAt()
    {
        $subscriber = new TimestampableSubscriber();
        $class = new \ReflectionClass($subscriber);
        $method = $class->getMethod('saveUpdatedAt');
        $method->setAccessible(true);

        $user = new User();

        $life = new LifecycleEventArgs(
            $user,
            $this->createMock(ObjectManager::class)
        );

        $method->invokeArgs($subscriber, [$life]);

        $this->assertNotNull($user->getUpdatedAt());
    }

    public function testSaveUpdatedAtNotSupported()
    {
        $subscriber = new TimestampableSubscriber();
        $class = new \ReflectionClass($subscriber);
        $method = $class->getMethod('saveUpdatedAt');
        $method->setAccessible(true);

        $filterUser = new FilterPlace();

        $life = new LifecycleEventArgs(
            $filterUser,
            $this->createMock(ObjectManager::class)
        );

        $before = clone $filterUser;

        $method->invokeArgs($subscriber, [$life]);

        $this->assertEquals($before, $filterUser);
    }

    public function testSaveCreatedAt()
    {
        $subscriber = new TimestampableSubscriber();
        $class = new \ReflectionClass($subscriber);
        $method = $class->getMethod('saveCreatedAt');
        $method->setAccessible(true);

        $user = new User();

        $life = new LifecycleEventArgs(
            $user,
            $this->createMock(ObjectManager::class)
        );

        $method->invokeArgs($subscriber, [$life]);

        $this->assertNotNull($user->getCreatedAt());
    }

    public function testSaveCreatedAtNotSupported()
    {
        $subscriber = new TimestampableSubscriber();
        $class = new \ReflectionClass($subscriber);
        $method = $class->getMethod('saveCreatedAt');
        $method->setAccessible(true);

        $filterUser = new FilterPlace();

        $life = new LifecycleEventArgs(
            $filterUser,
            $this->createMock(ObjectManager::class)
        );

        $before = clone $filterUser;

        $method->invokeArgs($subscriber, [$life]);

        $this->assertEquals($before, $filterUser);
    }

    public function testSupports()
    {
        $subscriber = new TimestampableSubscriber();
        $class = new \ReflectionClass($subscriber);
        $method = $class->getMethod('supports');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($subscriber, [new User()]));
        $this->assertTrue($method->invokeArgs($subscriber, [$this->createMock(Timestampable::class)]));
        $this->assertFalse($method->invokeArgs($subscriber, [new FilterPlace()]));
    }

    public function testGetSubscribedEvents()
    {
        $subscriber = new TimestampableSubscriber();
        $this->assertSame(['prePersist', 'preUpdate'], $subscriber->getSubscribedEvents());
    }
}
