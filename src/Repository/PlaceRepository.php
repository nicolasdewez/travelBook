<?php

namespace App\Repository;

use App\Entity\User;
use App\Model\FilterPlace;
use App\Pagination\DefinitionPagination;
use Doctrine\ORM\EntityRepository;

class PlaceRepository extends EntityRepository
{
    /**
     * @param FilterPlace $filterPlace
     *
     * @return int
     */
    public function countByCriteria(FilterPlace $filterPlace): int
    {
        $parameters = [];

        $query = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
        ;

        if (null !== $filterPlace->getTitle()) {
            $query->andWhere('LOWER(p.title) LIKE :title');
            $parameters['title'] = sprintf('%%%s%%', strtolower($filterPlace->getTitle()));
        }

        return $query->getQuery()
            ->setParameters($parameters)
            ->getSingleScalarResult()
        ;
    }

    /**
     * @param FilterPlace $filterPlace
     * @param array       $pagination
     *
     * @return User[]
     */
    public function getByCriteria(FilterPlace $filterPlace, array $pagination): array
    {
        $parameters = [];

        $query = $this->createQueryBuilder('p')
            ->setMaxResults($pagination[DefinitionPagination::INDEX_LIMIT])
            ->setFirstResult($pagination[DefinitionPagination::INDEX_OFFSET])
        ;

        if (null !== $filterPlace->getTitle()) {
            $query->andWhere('LOWER(p.title) LIKE :title');
            $parameters['title'] = sprintf('%%%s%%', strtolower($filterPlace->getTitle()));
        }

        if (null !== $filterPlace->getSort()) {
            $query->orderBy(sprintf('p.%s', $filterPlace->getSort()));
        }

        return $query->getQuery()
            ->setParameters($parameters)
            ->execute()
        ;
    }
}
