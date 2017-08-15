<?php

namespace App\Tests\Pagination;

use App\Pagination\DefinitionPagination;
use App\Pagination\InformationPagination;
use PHPUnit\Framework\TestCase;

class InformationPaginationTest extends TestCase
{
    public function testGetLimitAndOffset()
    {
        $pagination = new InformationPagination();

        $this->assertSame([
            DefinitionPagination::INDEX_LIMIT => DefinitionPagination::ELEMENT_BY_PAGE,
            DefinitionPagination::INDEX_OFFSET => 0,
        ], $pagination->getLimitAndOffset(1));

        $this->assertSame([
            DefinitionPagination::INDEX_LIMIT => DefinitionPagination::ELEMENT_BY_PAGE,
            DefinitionPagination::INDEX_OFFSET => 50,
        ], $pagination->getLimitAndOffset(3));
    }

    public function testGetNbPages()
    {
        $pagination = new InformationPagination();

        $this->assertSame(0, $pagination->getNbPages(0));
        $this->assertSame(1, $pagination->getNbPages(1));
        $this->assertSame(1, $pagination->getNbPages(DefinitionPagination::ELEMENT_BY_PAGE));
        $this->assertSame(2, $pagination->getNbPages(DefinitionPagination::ELEMENT_BY_PAGE + 1));
    }
}
