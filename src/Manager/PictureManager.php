<?php

namespace App\Manager;

use App\Entity\Picture;
use App\Entity\User;
use App\Logger\Log;
use App\Model\FilterPicture;
use App\Pagination\InformationPagination;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class PictureManager
{
    /** @var EntityManagerInterface */
    private $manager;

    /** @var PictureRepository */
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
        $this->repository = $manager->getRepository(Picture::class);
        $this->pagination = $pagination;
        $this->logger = $logger;
    }

    /**
     * @param Picture $picture
     */
    public function save(Picture $picture)
    {
        if (null === $picture->getId()) {
            $this->manager->persist($picture);
        }

        $this->manager->flush();

        $this->logger->info(sprintf('[%s] %d', Log::SUBJECT_SAVE_PICTURE, $picture->getId()));
    }

    /**
     * @param FilterPicture $filterPicture
     *
     * @return int
     */
    public function countToValidationElements(FilterPicture $filterPicture): int
    {
        return $this->repository->countToValidationByCriteria($filterPicture);
    }

    /**
     * @param FilterPicture $filterPicture
     *
     * @return int
     */
    public function countToReValidationElements(FilterPicture $filterPicture): int
    {
        return $this->repository->countToReValidationByCriteria($filterPicture);
    }

    /**
     * @param FilterPicture $filterPicture
     * @param int           $page
     *
     * @return array
     */
    public function listToValidationElements(FilterPicture $filterPicture, int $page): array
    {
        $pagination = $this->pagination->getLimitAndOffset($page);

        return $this->repository->getToValidationByCriteria($filterPicture, $pagination);
    }

    /**
     * @param FilterPicture $filterPicture
     * @param int           $page
     *
     * @return array
     */
    public function listToReValidationElements(FilterPicture $filterPicture, int $page): array
    {
        $pagination = $this->pagination->getLimitAndOffset($page);

        return $this->repository->getToReValidationByCriteria($filterPicture, $pagination);
    }

    /**
     * @param User $user
     *
     * @return Picture[]
     */
    public function listByUser(User $user): array
    {
        return $this->repository->getByUser($user);
    }
}
