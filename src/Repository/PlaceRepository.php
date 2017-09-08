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

        if (null !== $filterPlace->getLocale()) {
            $query->andWhere('p.locale = :locale');
            $parameters['locale'] = $filterPlace->getLocale();
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

        if (null !== $filterPlace->getLocale()) {
            $query->andWhere('p.locale = :locale');
            $parameters['locale'] = $filterPlace->getLocale();
        }

        if (null !== $filterPlace->getSort()) {
            $sort = explode('|', $filterPlace->getSort());
            $query->orderBy(sprintf('p.%s', $sort[0]), $sort[1]);
        }

        return $query->getQuery()
            ->setParameters($parameters)
            ->execute()
        ;
    }
}
