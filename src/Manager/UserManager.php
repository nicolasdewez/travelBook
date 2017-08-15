<?php

namespace App\Manager;

use App\Entity\User;
use App\Model\FilterUser;
use App\Pagination\InformationPagination;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserManager
{
    /** @var UserRepository */
    private $repository;

    /** @var InformationPagination */
    private $pagination;

    /**
     * @param EntityManagerInterface $manager
     * @param InformationPagination  $pagination
     */
    public function __construct(EntityManagerInterface $manager, InformationPagination $pagination)
    {
        $this->repository = $manager->getRepository(User::class);
        $this->pagination = $pagination;
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
