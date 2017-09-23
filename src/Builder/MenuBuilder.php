<?php

namespace App\Builder;

use App\Model\MenuItem;
use App\Security\Role;
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
        $items[] = $this->buildItem('menu.new_travel', 'app_travels_create');
        $items[] = $this->buildItem('menu.my_pictures', ''); //'app_pictures');
        $items[] = $this->buildItem('menu.search', ''); //'app_search');

        if (!$this->isAdminUser($roles) && !$this->isValidatorUser($roles)) {
            return $items;
        }

        if ($this->isAdminUser($roles)) {
            $adminItems = [
                $this->buildItem('menu.admin.users', 'app_admin_users_list'),
                $this->buildItem('menu.admin.places', 'app_admin_places_list'),
            ];

            $adminItem = new MenuItem(
                $this->translator->trans('menu.admin.title'),
                '',
                false,
                $adminItems
            );

            $adminItem->setActiveFromItems();

            $items[] = $adminItem;
        }

        if ($this->isValidatorUser($roles)) {
            $validatorItems = [
                $this->buildItem('menu.validator.pictures', 'app_validation_pictures_list'),
                $this->buildItem('menu.validator.pictures_processed', 'app_validation_pictures_list_processed'),
            ];

            $validatorItem = new MenuItem(
                $this->translator->trans('menu.validator.title'),
                '',
                false,
                $validatorItems
            );

            $validatorItem->setActiveFromItems();

            $items[] = $validatorItem;
        }

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
        return in_array(Role::ADMIN, $roles);
    }

    /**
     * @param array $roles
     *
     * @return bool
     */
    private function isValidatorUser(array $roles): bool
    {
        return in_array(Role::VALIDATOR, $roles);
    }
}
