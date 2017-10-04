<?php

namespace App\Session;

use App\Model\FilterFeedback;
use App\Model\FilterPicture;
use App\Model\FilterPlace;
use App\Model\FilterUser;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FilterStorage
{
    const FILTER_USER = 'app.filter.user';
    const FILTER_PLACE = 'app.filter.place';
    const FILTER_PICTURE = 'app.filter.picture';
    const FILTER_PICTURE_PROCESSED = 'app.filter.picture_processed';
    const FILTER_FEEDBACK = 'app.filter.feedback';

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
     * @return FilterUser
     */
    public function getFilterUser(): FilterUser
    {
        return $this->session->get(self::FILTER_USER, new FilterUser());
    }

    /**
     * @param FilterPlace $filterPlace
     */
    public function saveFilterPlace(FilterPlace $filterPlace)
    {
        $this->session->set(self::FILTER_PLACE, $filterPlace);
    }

    /**
     * @return FilterPlace
     */
    public function getFilterPlace(): FilterPlace
    {
        return $this->session->get(self::FILTER_PLACE, new FilterPlace());
    }

    /**
     * @param FilterPicture $filterPicture
     */
    public function saveFilterPicture(FilterPicture $filterPicture)
    {
        $this->session->set(self::FILTER_PICTURE, $filterPicture);
    }

    /**
     * @return FilterPicture
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

    /**
     * @param FilterFeedback $filterFeedback
     */
    public function saveFilterFeedback(FilterFeedback $filterFeedback)
    {
        $this->session->set(self::FILTER_FEEDBACK, $filterFeedback);
    }

    /**
     * @return FilterFeedback|null
     */
    public function getFilterFeedback(): FilterFeedback
    {
        return $this->session->get(self::FILTER_FEEDBACK, new FilterFeedback());
    }
}
