<?php

namespace App\Renderer;

interface RendererInterface
{
    /**
     * @param array $items
     *
     * @return string
     */
    public function execute(array $items): string;
}
