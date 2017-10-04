<?php

namespace App\Manager;

use App\Entity\Feedback;
use App\Logger\Log;
use App\Model\FilterFeedback;
use App\Pagination\InformationPagination;
use App\Repository\FeedbackRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class FeedbackManager
{
    /** @var EntityManagerInterface */
    private $manager;

    /** @var FeedbackRepository */
    private $repository;

    /** @var InformationPagination */
    private $pagination;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param EntityManagerInterface $manager
     * @param InformationPagination  $pagination
     * @param LoggerInterface        $logger
     */
    public function __construct(EntityManagerInterface $manager, InformationPagination $pagination, LoggerInterface $logger)
    {
        $this->manager = $manager;
        $this->repository = $manager->getRepository(Feedback::class);
        $this->pagination = $pagination;
        $this->logger = $logger;
    }

    /**
     * @param Feedback $feedback
     */
    public function save(Feedback $feedback)
    {
        if (null === $feedback->getId()) {
            $this->manager->persist($feedback);
        }

        $this->manager->flush();

        $this->logger->info(sprintf(
            '[%s] Sent by %s (%d)',
            Log::SUBJECT_SAVE_FEEDBACK,
            $feedback->getUser()->getUsername(),
            $feedback->getId()
        ));
    }

    /**
     * @param FilterFeedback $filterFeedback
     *
     * @return int
     */
    public function countElements(FilterFeedback $filterFeedback): int
    {
        return $this->repository->countByCriteria($filterFeedback);
    }

    /**
     * @param FilterFeedback $filterFeedback
     * @param int            $page
     *
     * @return array
     */
    public function listElements(FilterFeedback $filterFeedback, int $page): array
    {
        $pagination = $this->pagination->getLimitAndOffset($page);

        return $this->repository->getByCriteria($filterFeedback, $pagination);
    }
}
