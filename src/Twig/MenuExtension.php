<?php

namespace App\Twig;

use App\Builder\MenuBuilder as Builder;
use App\Renderer\MenuRenderer as Renderer;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MenuExtension extends AbstractExtension
{
    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var Builder */
    private $builder;

    /** @var Renderer */
    private $renderer;

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param Builder               $builder
     * @param Renderer              $renderer
     */
    public function __construct(TokenStorageInterface $tokenStorage, Builder $builder, Renderer $renderer)
    {
        $this->tokenStorage = $tokenStorage;
        $this->builder = $builder;
        $this->renderer = $renderer;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('display_menu', [$this, 'displayMenu']),
        ];
    }

    /**
     * @return string
     */
    public function displayMenu(): string
    {
        if (!$this->isUserConnected()) {
            return '';
        }

        $items = $this->builder->execute($this->getRolesUser());

        return $this->renderer->execute($items);
    }

    /**
     * @return bool
     */
    private function isUserConnected(): bool
    {
        $token = $this->tokenStorage->getToken();
        if (null === $token) {
            return false;
        }

        $user = $token->getUser();

        return null !== $user && $user instanceof AdvancedUserInterface;
    }

    /**
     * @return array
     */
    private function getRolesUser(): array
    {
        if (!$this->isUserConnected()) {
            return [];
        }

        return $this->tokenStorage->getToken()->getUser()->getRoles();
    }
}
