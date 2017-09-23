<?php

namespace App\EventSubscriber\Entity;

use App\Entity\Picture;
use App\Uploader\PictureUploader;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureUploadSubscriber implements EventSubscriber
{
    /** @var PictureUploader */
    private $uploader;

    /**
     * @param PictureUploader $uploader
     */
    public function __construct(PictureUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$this->supports($entity)) {
            return;
        }

        $file = $entity->getFile();

        // only upload new files
        if (!($file instanceof UploadedFile)) {
            return;
        }

        $fileName = $this->uploader->execute($file);
        $entity->setName($fileName);
    }

    /**
     * @param object $entity
     *
     * @return bool
     */
    private function supports($entity)
    {
        return $entity instanceof Picture;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return ['prePersist'];
    }
}
