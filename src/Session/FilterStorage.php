<?php

namespace App\Session;

use App\Model\FilterPicture;
use App\Model\FilterUser;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FilterStorage
{
    const FILTER_USER = 'app.filter.user';
    const FILTER_PICTURE = 'app.filter.picture';
    const FILTER_PICTURE_PROCESSED = 'app.filter.picture_processed';

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

    /**
     * @param FilterPicture $filterPicture
     */
    public function saveFilterPicture(FilterPicture $filterPicture)
    {
        $this->session->set(self::FILTER_PICTURE, $filterPicture);
    }

    /**
     * @return FilterPicture|null
     */
    public function getFilterPicture(): FilterPicture
    {
        return $this->session->get(self::FILTER_PICTURE, new FilterPicture());
    }

    /**
     * @param FilterPicture $filterPicture
     */
    public function saveFilterPictureProcessed(FilterPicture $filterPicture)
    {
        $this->session->set(self::FILTER_PICTURE_PROCESSED, $filterPicture);
    }

    /**
     * @return FilterPicture|null
     */
    public function getFilterPictureProcessed(): FilterPicture
    {
        return $this->session->get(self::FILTER_PICTURE_PROCESSED, new FilterPicture());
    }
}
