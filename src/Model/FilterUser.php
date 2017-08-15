<?php

namespace App\Model;

class FilterUser
{
    /** @var string */
    private $username;

    /** @var string */
    private $locale;

    /** @var string */
    private $role;

    /** @var bool */
    private $enabled;

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
     * @return FilterUser
     */
    public function setUsername(?string $username): FilterUser
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     *
     * @return FilterUser
     */
    public function setLocale(?string $locale): FilterUser
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return string
     */
    public function getRole(): ?string
    {
        return $this->role;
    }

    /**
     * @param string $role
     *
     * @return FilterUser
     */
    public function setRole(?string $role): FilterUser
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     *
     * @return FilterUser
     */
    public function setEnabled(?bool $enabled): FilterUser
    {
        $this->enabled = $enabled;

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
     * @return FilterUser
     */
    public function setSort(?string $sort): FilterUser
    {
        $this->sort = $sort;

        return $this;
    }
}
