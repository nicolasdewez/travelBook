<?php

namespace App\Model;

class FilterFeedback
{
    /** @var string */
    private $username;

    /** @var string */
    private $subject;

    /** @var bool */
    private $processed;

    /** @var string */
    private $sort;

    /**
     * @return string
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return FilterFeedback
     */
    public function setUsername(?string $username): FilterFeedback
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     *
     * @return FilterFeedback
     */
    public function setSubject(?string $subject): FilterFeedback
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return bool
     */
    public function isProcessed(): ?bool
    {
        return $this->processed;
    }

    /**
     * @param bool $processed
     *
     * @return FilterFeedback
     */
    public function setProcessed(?bool $processed): FilterFeedback
    {
        $this->processed = $processed;

        return $this;
    }

    /**
     * @return string
     */
    public function getSort(): ?string
    {
        return $this->sort;
    }

    /**
     * @param string $sort
     *
     * @return FilterFeedback
     */
    public function setSort(?string $sort): FilterFeedback
    {
        $this->sort = $sort;

        return $this;
    }
}
