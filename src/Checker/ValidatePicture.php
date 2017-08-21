<?php

namespace App\Checker;

use App\Entity\Picture;
use App\Logger\Log;
use App\Workflow\CheckPictureWorkflow;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class ValidatePicture
{
    /** @var EntityManagerInterface */
    private $manager;

    /** @var CheckPictureWorkflow */
    private $workflow;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param EntityManagerInterface $manager
     * @param CheckPictureWorkflow   $workflow
     * @param LoggerInterface        $logger
     */
    public function __construct(EntityManagerInterface $manager, CheckPictureWorkflow $workflow, LoggerInterface $logger)
    {
        $this->manager = $manager;
        $this->workflow = $workflow;
        $this->logger = $logger;
    }

    /**
     * @param Picture $picture
     */
    public function execute(Picture $picture)
    {
        if (!$this->workflow->canApplyValidation($picture)) {
            $this->logger->error(sprintf(
                'Picture %d can not be validated because workflow not support this.',
                $picture->getId()
            ));

            return;
        }

        $this->workflow->applyValidation($picture);

        $this->manager->flush();

        $this->logger->info(sprintf('[%s] Picture %d', Log::SUBJECT_PICTURE_VALIDATION, $picture->getId()));
    }
}
