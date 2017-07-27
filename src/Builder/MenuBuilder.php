<?php

namespace App\Builder;

use App\Model\MenuItem;
use App\Security\Roles;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class MenuBuilder
{
    /** @var RequestStack */
    private $request;

    /** @var RouterInterface */
    private $router;

    /** @var TranslatorInterface */
    private $translator;

    /**
     * @param RequestStack        $request
     * @param RouterInterface     $router
     * @param TranslatorInterface $translator
     */
    public function __construct(RequestStack $request, RouterInterface $router, TranslatorInterface $translator)
    {
        $this->request = $request;
        $this->router = $router;
        $this->translator = $translator;
    }

    /**
     * @param array $roles
     *
     * @return array
     */
    public function execute(array $roles): array
    {
        $items = [];

        $items[] = $this->buildItem('menu.my_travels', 'app_travels');
        $items[] = $this->buildItem('menu.new_travel', ''); //'app_travels_new');
        $items[] = $this->buildItem('menu.my_pictures', ''); //'app_pictures');
        $items[] = $this->buildItem('menu.search', ''); //'app_search');

        if (!$this->isAdminUser($roles)) {
            return $items;
        }

        $adminItems = [];
        $adminItems[] = $this->buildItem('menu.admin.users', ''); //'app_admin_users');

        if ($this->isValidatorUser($roles)) {
            $adminItems[] = $this->buildItem('menu.admin.pictures_validator', ''); //'app_admin_validator_pictures');
        }

        $adminItem = new MenuItem(
            $this->translator->trans('menu.admin.title'),
            '',
            false,
            $adminItems
        );

        $adminItem->setActiveFromItems();

        $items[] = $adminItem;

        return $items;
    }

    /**
     * @param string $title
     * @param string $route
     *
     * @return MenuItem
     */
    private function buildItem(string $title, string $route): MenuItem
    {
        $currentRoute = $this->router->match($this->request->getCurrentRequest()->getPathInfo())['_route'];

        return new MenuItem(
            $this->translator->trans($title),
            '' !== $route ? $this->router->generate($route) : '',
            $currentRoute === $route
        );
    }

    /**
     * @param array $roles
     *
     * @return bool
     */
    private function isAdminUser(array $roles): bool
    {
        return in_array(Roles::ROLE_ADMIN, $roles) || in_array(Roles::ROLE_VALIDATOR, $roles);
    }

    /**
     * @param array $roles
     *
     * @return bool
     */
    private function isValidatorUser(array $roles): bool
    {
        return in_array(Roles::ROLE_VALIDATOR, $roles);
    }
}
