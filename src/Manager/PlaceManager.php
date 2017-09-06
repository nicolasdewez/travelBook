<?php

namespace App\Manager;

use App\Entity\Place;
use App\Logger\Log;
use App\Model\FilterPlace;
use App\Pagination\InformationPagination;
use App\Repository\PlaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class PlaceManager
{
    /** @var EntityManagerInterface */
    private $manager;

    /** @var PlaceRepository */
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
        $this->repository = $manager->getRepository(Place::class);
        $this->pagination = $pagination;
        $this->logger = $logger;
    }

    /**
     * @param Place $place
     */
    public function save(Place $place)
    {
        if (null === $place->getId()) {
            $this->manager->persist($place);
        }

        $this->manager->flush();

        $this->logger->info(sprintf('[%s] %s (%d)', Log::SUBJECT_SAVE_PLACE, $place->getTitle(), $place->getId()));
    }

    /**
     * @param FilterPlace $filterPlace
     *
     * @return int
     */
    public function countElements(FilterPlace $filterPlace): int
    {
        return $this->repository->countByCriteria($filterPlace);
    }

    /**
     * @param FilterPlace $filterPlace
     * @param int         $page
     *
     * @return array
     */
    public function listElements(FilterPlace $filterPlace, int $page): array
    {
        $pagination = $this->pagination->getLimitAndOffset($page);

        return $this->repository->getByCriteria($filterPlace, $pagination);
    }
}
