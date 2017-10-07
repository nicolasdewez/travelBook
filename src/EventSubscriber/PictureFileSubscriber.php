<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Picture;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class PictureFileSubscriber implements EventSubscriberInterface
{
    /** @var string */
    private $pathPictures;

    /**
     * @param string $pathPictures
     */
    public function __construct(string $pathPictures)
    {
        $this->pathPictures = $pathPictures;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function addFile(GetResponseEvent $event)
    {
        $picture = $event->getRequest()->attributes->get('data');
        $method = $event->getRequest()->getMethod();

        if (!$picture instanceof Picture || Request::METHOD_GET !== $method) {
            return;
        }

        $file = realpath(sprintf('%s/%s', $this->pathPictures, $picture->getName()));
        if (false === $file) {
            return;
        }

        $picture->setContent(base64_encode(file_get_contents($file)));
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['addFile', EventPriorities::POST_READ],
        ];
    }
}
