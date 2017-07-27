<?php

namespace App\Renderer;

use Twig\Environment as Twig;

class BreadcrumbRenderer implements RendererInterface
{
    /** @var Twig */
    private $twig;

    /**
     * @param Twig $twig
     */
    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(array $items): string
    {
        return $this->twig->render('common/breadcrumb.html.twig', ['items' => $items]);
    }
}
