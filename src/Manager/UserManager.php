<?php

namespace App\Manager;

use App\Entity\User;
use App\Logger\Log;
use App\Model\FilterUser;
use App\Pagination\InformationPagination;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class UserManager
{
    /** @var EntityManagerInterface */
    private $manager;

    /** @var UserRepository */
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
        $this->repository = $manager->getRepository(User::class);
        $this->pagination = $pagination;
        $this->logger = $logger;
    }

    /**
     * @param User $user
     */
    public function save(User $user)
    {
        if (null === $user->getId()) {
            $this->manager->persist($user);
        }

        $this->manager->flush();

        $this->logger->info(sprintf('[%s] %s (%d)', Log::SUBJECT_SAVE_USER, $user->getUsername(), $user->getId()));
    }

    /**
     * @param FilterUser $filterUser
     *
     * @return int
     */
    public function countElements(FilterUser $filterUser): int
    {
        return $this->repository->countByCriteria($filterUser);
    }

    /**
     * @param FilterUser $filterUser
     * @param int        $page
     *
     * @return array
     */
    public function listElements(FilterUser $filterUser, int $page): array
    {
        $pagination = $this->pagination->getLimitAndOffset($page);

        return $this->repository->getByCriteria($filterUser, $pagination);
    }
}
