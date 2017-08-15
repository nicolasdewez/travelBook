<?php

namespace App\Session;

use App\Model\FilterUser;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FilterStorage
{
    const FILTER_USER = 'app.filter.user';

    /** @var SessionInterface */
    private $session;

    /**
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @param FilterUser $filterUser
     */
    public function saveFilterUser(FilterUser $filterUser)
    {
        $this->session->set(self::FILTER_USER, $filterUser);
    }

    /**
     * @return FilterUser|null
     */
    public function getFilterUser(): FilterUser
    {
        return $this->session->get(self::FILTER_USER, new FilterUser());
    }
}
