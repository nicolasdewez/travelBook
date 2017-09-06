<?php

namespace App\Renderer;

interface LinkShowRendererInterface
{
    /**
     * @param float $latitude
     * @param float $longitude
     *
     * @return string
     */
    public function execute(float $latitude, float $longitude): string;
}
