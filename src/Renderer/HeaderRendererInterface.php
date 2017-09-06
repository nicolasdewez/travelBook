<?php

namespace App\Renderer;

interface HeaderRendererInterface
{
    /**
     * @param array $items
     *
     * @return string
     */
    public function execute(array $items): string;
}
