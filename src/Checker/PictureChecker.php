<?php

namespace App\Checker;

use App\Entity\Picture;
use App\Mailer\PictureIsVirusMailer;
use App\Workflow\CheckPictureWorkflow;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class PictureChecker
{
    /** @var EntityManagerInterface */
    private $manager;

    /** @var VirusChecker */
    private $checker;

    /** @var PictureIsVirusMailer */
    private $mailer;

    /** @var CheckPictureWorkflow */
    private $workflow;

    /** @var LoggerInterface */
    private $logger;

    /** @var string */
    private $pathPictures;

    /**
     * @param EntityManagerInterface $manager
     * @param VirusChecker           $checker
     * @param PictureIsVirusMailer   $mailer
     * @param CheckPictureWorkflow   $workflow
     * @param LoggerInterface        $logger
     * @param string                 $pathPictures
     */
    public function __construct(
        EntityManagerInterface $manager,
        VirusChecker $checker,
        PictureIsVirusMailer $mailer,
        CheckPictureWorkflow $workflow,
        LoggerInterface $logger,
        string $pathPictures
    ) {
        $this->manager = $manager;
        $this->checker = $checker;
        $this->mailer = $mailer;
        $this->workflow = $workflow;
        $this->logger = $logger;
        $this->pathPictures = $pathPictures;
    }

    /**
     * @param Picture $picture
     */
    public function execute(Picture $picture)
    {
        if (!$this->workflow->canApplyAnalyze($picture)) {
            $this->logger->error(sprintf(
                'Picture %d can not be analyze because workflow not support this.',
                $picture->getId()
            ));

            return;
        }

        $file = new \SplFileInfo(sprintf('%s/%d', $this->pathPictures, $picture->getId()));

        if (!$this->checker->execute($file)) {
            $this->workflow->applyAnalyzeKo($picture);

            $this->manager->flush();

            $this->mailer->execute($picture);

            return;
        }

        $this->workflow->applyAnalyzeOk($picture);

        $this->manager->flush();
    }
}
