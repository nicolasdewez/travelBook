<?php

namespace App\Twig;

use App\Builder\BreadcrumbBuilder as Builder;
use App\Renderer\BreadcrumbRenderer as Renderer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BreadcrumbExtension extends AbstractExtension
{
    /** @var Builder */
    private $builder;

    /** @var Renderer */
    private $renderer;

    /**
     * @param Builder  $builder
     * @param Renderer $renderer
     */
    public function __construct(Builder $builder, Renderer $renderer)
    {
        $this->builder = $builder;
        $this->renderer = $renderer;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('display_breadcrumb', [$this, 'displayBreadcrumb']),
        ];
    }

    /**
     * @return string
     */
    public function displayBreadcrumb(): string
    {
        $items = $this->builder->execute();

        return $this->renderer->execute($items);
    }
}
