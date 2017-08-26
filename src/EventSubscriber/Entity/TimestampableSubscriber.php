<?php

namespace App\EventSubscriber\Entity;

use App\Entity\Timestampable;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class TimestampableSubscriber implements EventSubscriber
{
    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->saveCreatedAt($args);
        $this->saveUpdatedAt($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->saveUpdatedAt($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    private function saveCreatedAt(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$this->supports($entity)) {
            return;
        }

        $entity->setCreatedAt(new \DateTime());
    }

    /**
     * @param LifecycleEventArgs $args
     */
    private function saveUpdatedAt(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$this->supports($entity)) {
            return;
        }

        $entity->setUpdatedAt(new \DateTime());
    }

    /**
     * @param object $entity
     *
     * @return bool
     */
    private function supports($entity)
    {
        return $entity instanceof Timestampable;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return ['prePersist', 'preUpdate'];
    }
}
