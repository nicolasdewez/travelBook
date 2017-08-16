<?php

namespace App\Builder;

use App\Model\BreadcrumbItem;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Yaml\Yaml;

class BreadcrumbBuilder
{
    /** @var RequestStack */
    private $request;

    /** @var RouterInterface */
    private $router;

    /** @var TranslatorInterface */
    private $translator;

    /** @var LoggerInterface */
    private $logger;

    /** @var string */
    private $configPath;

    /**
     * @param RequestStack        $request
     * @param RouterInterface     $router
     * @param LoggerInterface     $logger
     * @param TranslatorInterface $translator
     * @param string              $configPath
     */
    public function __construct(
        RequestStack $request,
        RouterInterface $router,
        TranslatorInterface $translator,
        LoggerInterface $logger,
        string $configPath
    ) {
        $this->request = $request;
        $this->router = $router;
        $this->translator = $translator;
        $this->logger = $logger;
        $this->configPath = $configPath;
    }

    /**
     * @return array
     */
    public function execute(): array
    {
        $config = $this->loadConfigFile();
        $currentRoute = $this->router->match($this->request->getCurrentRequest()->getPathInfo())['_route'];
        $firstElement = true;

        if (!isset($config[$currentRoute])) {
            return [];
        }

        $items = [];
        do {
            $configItem = $config[$currentRoute];

            $items[] = $this->buildItem($configItem, $currentRoute, $firstElement);

            $currentRoute = isset($config[$currentRoute]['parent']) ? $config[$currentRoute]['parent'] : null;
            $firstElement = false;
        } while (null !== $currentRoute);

        return array_reverse($items);
    }

    /**
     * @param array  $config
     * @param string $currentRoute
     * @param bool   $firstElement
     *
     * @return BreadcrumbItem
     */
    private function buildItem(array $config, string $currentRoute, bool $firstElement): BreadcrumbItem
    {
        $url = '#';
        if (!$firstElement) {
            $url = $this->router->generate($currentRoute, isset($config['route_params']) ? $config['route_params'] : []);
        }

        return new BreadcrumbItem(
            $this->translator->trans($config['title'], isset($config['title_params']) ? $config['title_params'] : []),
            $url,
            true === $firstElement
        );
    }

    /**
     * @return array
     */
    private function loadConfigFile(): array
    {
        if (false === realpath($this->configPath)) {
            $this->logger->error(sprintf('Config breadcrumb file %s does not exists.', $this->configPath));

            return [];
        }

        return Yaml::parse(file_get_contents($this->configPath));
    }
}
