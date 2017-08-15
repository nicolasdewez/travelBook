<?php

namespace App\Repository;

use App\Entity\User;
use App\Model\FilterUser;
use App\Pagination\DefinitionPagination;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    /**
     * @param FilterUser $filterUser
     *
     * @return int
     */
    public function countByCriteria(FilterUser $filterUser): int
    {
        $parameters = [];

        $query = $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
        ;

        if (null !== $filterUser->getUsername()) {
            $query->andWhere('LOWER(u.username) LIKE :username');
            $parameters['username'] = sprintf('%s%%', strtolower($filterUser->getUsername()));
        }

        if (null !== $filterUser->getLocale()) {
            $query->andWhere('u.locale = :locale');
            $parameters['locale'] = $filterUser->getLocale();
        }

        if (null !== $filterUser->getRole()) {
            $query->andWhere('u.roles LIKE :role');
            $parameters['role'] = sprintf('%%%s%%', $filterUser->getRole());
        }

        if (null !== $filterUser->isEnabled()) {
            $query->andWhere('u.enabled = :enabled');
            $parameters['enabled'] = $filterUser->isEnabled();
        }

        return $query->getQuery()
            ->setParameters($parameters)
            ->getSingleScalarResult()
        ;
    }

    /**
     * @param FilterUser $filterUser
     * @param array      $pagination
     *
     * @return User[]
     */
    public function getByCriteria(FilterUser $filterUser, array $pagination): array
    {
        $parameters = [];

        $query = $this->createQueryBuilder('u')
            ->setMaxResults($pagination[DefinitionPagination::INDEX_LIMIT])
            ->setFirstResult($pagination[DefinitionPagination::INDEX_OFFSET])
        ;

        if (null !== $filterUser->getUsername()) {
            $query->andWhere('LOWER(u.username) LIKE :username');
            $parameters['username'] = sprintf('%s%%', strtolower($filterUser->getUsername()));
        }

        if (null !== $filterUser->getLocale()) {
            $query->andWhere('u.locale = :locale');
            $parameters['locale'] = $filterUser->getLocale();
        }

        if (null !== $filterUser->getRole()) {
            $query->andWhere('u.roles LIKE :role');
            $parameters['role'] = sprintf('%%%s%%', $filterUser->getRole());
        }

        if (null !== $filterUser->isEnabled()) {
            $query->andWhere('u.enabled = :enabled');
            $parameters['enabled'] = $filterUser->isEnabled();
        }

        if (null !== $filterUser->getSort()) {
            $query->orderBy(sprintf('u.%s', $filterUser->getSort()));
        }

        return $query->getQuery()
            ->setParameters($parameters)
            ->execute()
        ;
    }
}
