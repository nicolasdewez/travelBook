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
        return $this->countByCriteriaAndStates($filterPicture, [CheckPictureDefinitionWorkflow::PLACE_HEALTHY]);
    }

    /**
     * @param FilterPicture $filterPicture
     *
     * @return int
     */
    public function countToReValidationByCriteria(FilterPicture $filterPicture): int
    {
        return $this->countByCriteriaAndStates(
            $filterPicture,
            [CheckPictureDefinitionWorkflow::PLACE_VALIDATED, CheckPictureDefinitionWorkflow::PLACE_INVALID]
        );
    }

    /**
     * @param FilterPicture $filterPicture
     * @param array         $states
     *
     * @return int
     */
    private function countByCriteriaAndStates(FilterPicture $filterPicture, array $states): int
    {
        $parameters = [];

        $query = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
        ;

        if (null !== $filterPicture->getTitle()) {
            $query->andWhere('LOWER(p.title) LIKE :title');
            $parameters['title'] = sprintf('%s%%', strtolower($filterPicture->getTitle()));
        }

        if (null !== $filterPicture->getUsername()) {
            $query
                ->innerJoin('p.travel', 't')
                ->innerJoin('t.user', 'u')
                ->andWhere('LOWER(u.username) LIKE :username')
            ;
            $parameters['username'] = sprintf('%s%%', strtolower($filterPicture->getUsername()));
        }

        if (null !== $filterPicture->getState()) {
            $query->andWhere('p.checkState = :check_state');
            $parameters['check_state'] = $filterPicture->getState();
        }

        $query->andWhere('p.checkState IN (:states)');
        $parameters['states'] = $states;

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
        return $this->getByCriteriaAndStates($filterPicture, $pagination, [CheckPictureDefinitionWorkflow::PLACE_HEALTHY]);
    }

    /**
     * @param FilterPicture $filterPicture
     * @param array         $pagination
     *
     * @return Picture[]
     */
    public function getToReValidationByCriteria(FilterPicture $filterPicture, array $pagination): array
    {
        return $this->getByCriteriaAndStates(
            $filterPicture,
            $pagination,
            [CheckPictureDefinitionWorkflow::PLACE_VALIDATED, CheckPictureDefinitionWorkflow::PLACE_INVALID]
        );
    }

    /**
     * @param FilterPicture $filterPicture
     * @param array         $pagination
     * @param array         $states
     *
     * @return Picture[]
     */
    private function getByCriteriaAndStates(FilterPicture $filterPicture, array $pagination, array $states): array
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

        if (null !== $filterPicture->getUsername()) {
            $query
                ->innerJoin('p.travel', 't')
                ->innerJoin('t.user', 'u')
                ->andWhere('LOWER(u.username) LIKE :username')
            ;
            $parameters['username'] = sprintf('%s%%', strtolower($filterPicture->getUsername()));
        }

        if (null !== $filterPicture->getState()) {
            $query->andWhere('p.checkState = :check_state');
            $parameters['check_state'] = $filterPicture->getState();
        }

        if (null !== $filterPicture->getSort()) {
            $sort = explode('|', $filterPicture->getSort());
            $query->orderBy(sprintf('p.%s', $sort[0]), $sort[1]);
        }

        $query->andWhere('p.checkState IN (:states)');
        $parameters['states'] = $states;

        return $query->getQuery()
            ->setParameters($parameters)
            ->execute()
        ;
    }
}
