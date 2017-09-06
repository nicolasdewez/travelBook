<?php

namespace App\Renderer;

use Twig\Environment as Twig;

class MenuRenderer implements HeaderRendererInterface
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
        return $this->twig->render('common/menu.html.twig', ['items' => $items]);
    }
}
