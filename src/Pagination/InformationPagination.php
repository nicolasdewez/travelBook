<?php

namespace App\Pagination;

class InformationPagination
{
    /**
     * @param int $page
     *
     * @return array
     */
    public function getLimitAndOffset(int $page): array
    {
        return [
            DefinitionPagination::INDEX_LIMIT => DefinitionPagination::ELEMENT_BY_PAGE,
            DefinitionPagination::INDEX_OFFSET => ($page - 1) * DefinitionPagination::ELEMENT_BY_PAGE,
        ];
    }

    /**
     * @param int $nbElements
     *
     * @return int
     */
    public function getNbPages(int $nbElements): int
    {
        return ceil($nbElements / DefinitionPagination::ELEMENT_BY_PAGE);
    }
}
