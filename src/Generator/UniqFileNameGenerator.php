<?php

namespace App\Generator;

class UniqFileNameGenerator
{
    /**
     * @return string
     */
    public function execute(): string
    {
        return md5(uniqid());
    }
}
