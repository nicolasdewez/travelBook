<?php

namespace App\Manager;

use App\Entity\Travel;
use App\Entity\User;
use App\Logger\Log;
use App\Repository\PlaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class TravelManager
{
    /** @var EntityManagerInterface */
    private $manager;

    /** @var PlaceRepository */
    private $repository;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param EntityManagerInterface $manager
     * @param LoggerInterface        $logger
     */
    public function __construct(EntityManagerInterface $manager, LoggerInterface $logger)
    {
        $this->manager = $manager;
        $this->repository = $manager->getRepository(Travel::class);
        $this->logger = $logger;
    }

    /**
     * @param Travel $travel
     */
    public function save(Travel $travel)
    {
        if (null === $travel->getId()) {
            $this->manager->persist($travel);
        }

        $this->manager->flush();

        $this->logger->info(sprintf('[%s] %s (%d)', Log::SUBJECT_SAVE_TRAVEL, $travel->getTitle(), $travel->getId()));
    }

    /**
     * @param User $user
     *
     * @return Travel[]
     */
    public function listByUser(User $user): array
    {
        return $this->repository->findBy(['user' => $user], ['id' => 'ASC']);
    }
}
