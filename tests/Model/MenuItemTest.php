<?php

namespace App\Tests\Model;

use App\Model\MenuItem;
use PHPUnit\Framework\TestCase;

class MenuItemTest extends TestCase
{
    public function testSetActiveFromItems()
    {
        // No sub items and active false
        $item = new MenuItem('title', null, false, []);
        $item->setActiveFromItems();

        $this->assertFalse($item->isActive());

        // No sub items and active true
        $item = new MenuItem('title', null, true, []);
        $item->setActiveFromItems();

        $this->assertFalse($item->isActive());

        // no sub items active and active false
        $item = new MenuItem('title', null, false, [(new MenuItem('title'))]);
        $item->setActiveFromItems();

        $this->assertFalse($item->isActive());

        // no sub items active and active true
        $item = new MenuItem('title', null, true, [(new MenuItem('title'))]);
        $item->setActiveFromItems();

        $this->assertFalse($item->isActive());

        // sub item active and active false
        $item = new MenuItem('title', null, false, [(new MenuItem('title', null, true))]);
        $item->setActiveFromItems();

        $this->assertTrue($item->isActive());

        // sub item active and active true
        $item = new MenuItem('title', null, true, [(new MenuItem('title', null, true))]);
        $item->setActiveFromItems();

        $this->assertTrue($item->isActive());
    }
}
