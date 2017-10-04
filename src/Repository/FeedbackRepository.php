<?php

namespace App\Repository;

use App\Entity\Feedback;
use App\Model\FilterFeedback;
use App\Pagination\DefinitionPagination;
use Doctrine\ORM\EntityRepository;

class FeedbackRepository extends EntityRepository
{
    /**
     * @param FilterFeedback $filterFeedback
     *
     * @return int
     */
    public function countByCriteria(FilterFeedback $filterFeedback): int
    {
        $parameters = [];

        $query = $this->createQueryBuilder('f')
            ->select('COUNT(f.id)')
        ;

        if (null !== $filterFeedback->getUsername()) {
            $query
                ->innerJoin('f.user', 'u')
                ->andWhere('LOWER(u.username) LIKE :username')
            ;
            $parameters['username'] = sprintf('%s%%', strtolower($filterFeedback->getUsername()));
        }

        if (null !== $filterFeedback->getSubject()) {
            $query->andWhere('LOWER(f.subject) LIKE :subject');
            $parameters['subject'] = sprintf('%%%s%%', strtolower($filterFeedback->getSubject()));
        }

        if (null !== $filterFeedback->isProcessed()) {
            $query->andWhere('f.processed = :processed');
            $parameters['processed'] = $filterFeedback->isProcessed();
        }

        return $query->getQuery()
            ->setParameters($parameters)
            ->getSingleScalarResult()
        ;
    }

    /**
     * @param FilterFeedback $filterFeedback
     * @param array          $pagination
     *
     * @return Feedback[]
     */
    public function getByCriteria(FilterFeedback $filterFeedback, array $pagination): array
    {
        $parameters = [];

        $query = $this->createQueryBuilder('f')
            ->setMaxResults($pagination[DefinitionPagination::INDEX_LIMIT])
            ->setFirstResult($pagination[DefinitionPagination::INDEX_OFFSET])
        ;

        if (null !== $filterFeedback->getUsername()) {
            $query
                ->innerJoin('f.user', 'u')
                ->andWhere('LOWER(u.username) LIKE :username')
            ;
            $parameters['username'] = sprintf('%s%%', strtolower($filterFeedback->getUsername()));
        }

        if (null !== $filterFeedback->getSubject()) {
            $query->andWhere('LOWER(f.subject) LIKE :subject');
            $parameters['subject'] = sprintf('%%%s%%', strtolower($filterFeedback->getSubject()));
        }

        if (null !== $filterFeedback->isProcessed()) {
            $query->andWhere('f.processed = :processed');
            $parameters['processed'] = $filterFeedback->isProcessed();
        }

        if (null !== $filterFeedback->getSort()) {
            $sort = explode('|', $filterFeedback->getSort());
            $query->orderBy(sprintf('f.%s', $sort[0]), $sort[1]);
        }

        return $query->getQuery()
            ->setParameters($parameters)
            ->execute()
        ;
    }
}
