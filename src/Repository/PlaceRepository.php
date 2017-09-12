<?php

namespace App\Repository;

use App\Entity\Place;
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
     * @return Place[]
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

    /**
     * @param string $query
     * @param string $locale
     *
     * @return Place[]
     */
    public function getByQuery(string $query, string $locale): array
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->setMaxResults(10)
            ->setFirstResult(0)
            ->andWhere('LOWER(p.title) LIKE :title')
            ->andWhere('p.locale = :locale')
            ->orderBy('p.title', 'ASC')
        ;

        $parameters['title'] = sprintf('%%%s%%', strtolower($query));
        $parameters['locale'] = $locale;

        return $queryBuilder->getQuery()
            ->setParameters($parameters)
            ->execute()
        ;
    }
}
