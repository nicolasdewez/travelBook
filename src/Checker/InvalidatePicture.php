<?php

namespace App\Checker;

use App\Entity\InvalidationPicture;
use App\Logger\Log;
use App\Producer\MailInvalidPictureProducer;
use App\Workflow\CheckPictureWorkflow;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class InvalidatePicture
{
    /** @var EntityManagerInterface */
    private $manager;

    /** @var CheckPictureWorkflow */
    private $workflow;

    /** @var MailInvalidPictureProducer */
    private $producer;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param EntityManagerInterface     $manager
     * @param CheckPictureWorkflow       $workflow
     * @param MailInvalidPictureProducer $producer
     * @param LoggerInterface            $logger
     */
    public function __construct(
        EntityManagerInterface $manager,
        CheckPictureWorkflow $workflow,
        MailInvalidPictureProducer $producer,
        LoggerInterface $logger
    ) {
        $this->manager = $manager;
        $this->workflow = $workflow;
        $this->producer = $producer;
        $this->logger = $logger;
    }

    /**
     * @param InvalidationPicture $invalidationPicture
     */
    public function execute(InvalidationPicture $invalidationPicture)
    {
        $picture = $invalidationPicture->getPicture();

        if (!$this->workflow->canApplyInvalidation($picture)) {
            $this->logger->error(sprintf(
                'Picture %d can not be validated because workflow not support this.',
                $picture->getId()
            ));

            return;
        }

        $this->workflow->applyInvalidation($picture);

        $this->manager->persist($invalidationPicture);
        $this->manager->flush();

        $this->producer->execute($invalidationPicture);

        $this->logger->info(sprintf(
            '[%s] Picture %d, reason %s %s',
            Log::SUBJECT_PICTURE_INVALIDATION,
            $picture->getId(),
            $invalidationPicture->getReason(),
            $invalidationPicture->getComment()
        ));
    }
}
