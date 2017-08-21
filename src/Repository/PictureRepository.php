<?php

namespace App\Repository;

use App\Entity\Picture;
use App\Model\FilterPicture;
use App\Pagination\DefinitionPagination;
use App\Workflow\CheckPictureDefinitionWorkflow;
use Doctrine\ORM\EntityRepository;

class PictureRepository extends EntityRepository
{
    /**
     * @param FilterPicture $filterPicture
     *
     * @return int
     */
    public function countToValidationByCriteria(FilterPicture $filterPicture): int
    {
        $parameters = [];

        $query = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
        ;

        if (null !== $filterPicture->getTitle()) {
            $query->andWhere('LOWER(p.title) LIKE :title');
            $parameters['title'] = sprintf('%s%%', strtolower($filterPicture->getTitle()));
        }

        $query->andWhere('p.checkState = :state');
        $parameters['state'] = CheckPictureDefinitionWorkflow::PLACE_HEALTHY;

        return $query->getQuery()
            ->setParameters($parameters)
            ->getSingleScalarResult()
        ;
    }

    /**
     * @param FilterPicture $filterPicture
     * @param array         $pagination
     *
     * @return Picture[]
     */
    public function getToValidationByCriteria(FilterPicture $filterPicture, array $pagination): array
    {
        $parameters = [];

        $query = $this->createQueryBuilder('p')
            ->setMaxResults($pagination[DefinitionPagination::INDEX_LIMIT])
            ->setFirstResult($pagination[DefinitionPagination::INDEX_OFFSET])
        ;

        if (null !== $filterPicture->getTitle()) {
            $query->andWhere('LOWER(p.title) LIKE :title');
            $parameters['title'] = sprintf('%s%%', strtolower($filterPicture->getTitle()));
        }

        if (null !== $filterPicture->getSort()) {
            $query->orderBy(sprintf('p.%s', $filterPicture->getSort()));
        }

        $query->andWhere('p.checkState = :state');
        $parameters['state'] = CheckPictureDefinitionWorkflow::PLACE_HEALTHY;

        return $query->getQuery()
            ->setParameters($parameters)
            ->execute()
        ;
    }
}
